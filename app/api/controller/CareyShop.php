<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    Api基类控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/20
 */

namespace app\api\controller;

use app\api\exception\ApiOutput;
use Exception;
use think\App;
use think\exception\ValidateException;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Db;
use think\Request;
use think\Validate;

abstract class CareyShop
{
    /**
     * Request实例
     * @var Request
     */
    protected $request;

    /**
     * 应用实例
     * @var App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 控制器错误信息
     * @var string
     */
    public $error;

    /**
     * AppKey
     * @var string
     */
    public $appkey;

    /**
     * AppSecret
     * @var string
     */
    public $appSecret;

    /**
     * Token
     * @var string
     */
    public $token;

    /**
     * Sign
     * @var string
     */
    public $sign;

    /**
     * 时间戳
     * @var int
     */
    public $timestamp;

    /**
     * 返回格式
     * @var string
     */
    public $format;

    /**
     * 业务方法
     * @var string
     */
    public $method;

    /**
     * 业务参数
     * @var array
     */
    public $params = [];

    /**
     * 方法路由器
     * @var array
     */
    protected static $route;

    /**
     * 对应模型
     * @var object
     */
    protected static $model;

    /**
     * 权限验证实例
     * @var object
     */
    protected static $auth;

    /**
     * 是否调试模式
     * @var bool
     */
    public $apiDebug = false;

    /**
     * 构造方法
     * @access public
     * @param App $app 应用对象
     * @throws
     */
    public function __construct(App $app)
    {
        // 注入
        $this->app = $app;
        $this->request = $this->app->request;

        // 验证请求方式
        if (!in_array($this->request->method(), ['GET', 'POST', 'OPTIONS'])) {
            $this->outputError('不支持的请求方式');
        }

        // 获取系统配置参数
        $setting = Cache::remember('setting', function () {
            return Db::name('setting')->withoutField('setting_id')->select();
        });

        if (!$setting) {
            Cache::delete('setting');
            $this->outputError('系统配置初始化失败');
        }

        $settingData = [];
        foreach ($setting as $value) {
            $settingData[$value['module']][$value['code']] = $value['value'];
        }

        if (count($settingData) > 0) {
            Config::set($settingData, convert_uudecode(')8V%R97ES:&]P `'));
        }

        // 跨域 OPTIONS 请求友好返回
        if ($this->request->isOptions()) {
            $this->outputError('success', 200);
        }

        // 检测是否开启API接口
        if (Config::get('careyshop.system_info.open_api') != 1) {
            $this->outputError(Config::get('careyshop.system_info.close_reason'));
        }

        // API_DEBUG模式是否运行
        $this->apiDebug = Config::get('app.api_debug', false);

        // 支持"text/plain"协议(仅限JSON)
        if (false !== strpos($this->request->contentType(), 'text/plain')) {
            $plain = json_decode($this->request->getInput(), true);
            $this->request->withPost($plain);
        }

        // 获取外部参数
        $this->params = $this->request->param();
        unset($this->params['version']);
        unset($this->params['controller']);
        unset($this->params[str_replace('.', '_', $_SERVER['REDIRECT_URL'])]);
        unset($this->params[$_SERVER['REDIRECT_URL']]);

        // 公共参数赋值
        $this->appkey = isset($this->params['appkey']) ? $this->params['appkey'] : '';
        $this->token = isset($this->params['token']) ? $this->params['token'] : '';
        $this->sign = isset($this->params['sign']) ? $this->params['sign'] : '';
        $this->timestamp = isset($this->params['timestamp']) ? $this->params['timestamp'] : 0;
        $this->format = !empty($this->params['format']) ? $this->params['format'] : 'json';
        $this->method = isset($this->params['method']) ? $this->params['method'] : '';
        ApiOutput::$format = $this->format;

//        // 验证Params
//        $validate = $this->apiDebug || $this->validate($this->params, 'CareyShop');
//        if (true !== $validate) {
//            $this->outputError($validate);
//        }
//
//        // 验证Token
//        $token = $this->checkToken();
//        if (true !== $token) {
//            // 未授权，请重新登录(401)
//            $this->outputError($token, 401);
//        }
//
//        // 验证Auth
//        $auth = $this->checkAuth();
//        if (true !== $auth) {
//            // 拒绝访问(403)
//            $this->outputError($auth, 403);
//        }
//
//        // 验证APP
//        $apps = $this->checkApp();
//        if (true !== $apps) {
//            $this->outputError($apps);
//        }
//
//        // 验证Sign
//        $sign = $this->apiDebug || $this->checkSign();
//        if (true !== $sign) {
//            $this->outputError($sign);
//        }

        // 控制器初始化
        static::init();
    }

    /**
     * 验证数据
     * @access protected
     * @param array        $data     数据
     * @param string|array $validate 验证器名或者验证规则数组
     * @param array        $message  提示信息
     * @param bool         $batch    是否批量验证
     * @return bool
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }

    /**
     * 自定义初始化
     * @access protected
     * @return void
     */
    protected static function init()
    {
    }

    /*
     * 方法路由器
     * @access protected
     * @return array
     */
    protected static function initMethod()
    {
        return [];
    }

    /**
     * api_debug模式下,尝试根据method命名规范查找模型方法(方便调试)
     * @access private
     * @return void
     */
    private function autoFindMethod()
    {
        if (!Config::get('app.api_debug', false)) {
            return;
        }

        foreach (self::$route as $value) {
            if (in_array($this->method, $value)) {
                self::$route = [$value[0] => [$value[0]]];
                !isset($value[1]) ?: self::$route[$value[0]][] = $value[1];
                return;
            }
        }

        $this->outputError(__FUNCTION__ . '模式,尝试查找的模型不存在');
    }

    /**
     * 核心基础首页
     * @access public
     * @return array
     */
    public function index()
    {
        // 获取控制器方法路由
        if (!isset(self::$route)) {
            self::$route = static::initMethod();
        }

        if (!array_key_exists($this->method, self::$route)) {
            $this->autoFindMethod();
        }

        // 删除多余数据,避免影响其他模块,并获取路由参数
        unset($this->params['appkey']);
        unset($this->params['token']);
        unset($this->params['timestamp']);
        unset($this->params['format']);
        unset($this->params['method']);

        // 调用自身成员函数或类成员方法
        $result = null;
        $callback = self::$route[$this->method];

        // 路由定义中如果数组[1]不存在,则表示默认对应model模型
        if (!isset($callback[1])) {
            $className = ucwords(str_replace(['-', '_'], ' ', $this->request->param('controller')));
            $className = str_replace(' ', '', $className);
            $callback[1] = 'app\\common\\model\\' . $className;
        }

        if (class_exists($callback[1])) {
            isset(static::$model) ?: static::$model = new $callback[1];
        } else if (false !== $callback[1]) {
            $this->outputError('模型类不存在');
        }

        try {
            if (method_exists($this, $callback[0])) {
                $result = call_user_func_array([$this, $callback[0]], []);
            } else if (method_exists(static::$model, $callback[0])) {
                $result = call_user_func([static::$model, $callback[0]], $this->getParams());
            } else {
                $this->outputError('method成员方法不存在');
            }
        } catch (Exception $e) {
            $result = false;
            $this->error = $e->getMessage();
        }

        // 记录日志
        if (!is_null(self::$auth)) {
            $logError = empty($this->error) && isset(static::$model) ? static::$model->getError() : $this->error;
            self::$auth->saveLog($this->getAuthUrl(), $this->request, $result, get_called_class(), $logError);
        }

        // 输出结果
        if (false === $result && !isset($result['callback_return_type'])) {
            !empty($this->error) || !is_object(static::$model) ?: $this->error = static::$model->getError();
            $this->outputError($this->error);
        }

        return $this->outputResult($result);
    }

    /*
     * 设置控制器错误信息
     * @access public
     * @param  string $value 错误信息
     * @return false
     */
    public function setError($value)
    {
        $this->error = $value;
        return false;
    }

    /*
     * 获取控制器错误信息
     * @access public
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
}
