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

use think\App;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Db;
use think\Request;

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
            Config::set($settingData, 'careyshop');
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
        $this->apiDebug = Config::has('app.api_debug') ? Config::get('app.api_debug') : false;

        // 支持"text/plain"协议(仅限JSON)
        if (false !== strpos($this->request->contentType(), 'text/plain')) {
            $plain = json_decode($this->request->getInput(), true);
            $this->request->withPost($plain);
        }

        // 获取外部参数
        $this->params = $this->request->param();
        unset($this->params['version']);
        unset($this->params['controller']);
//        unset($this->params[str_replace('.', '_', $_SERVER['PATH_INFO'])]);
//        unset($this->params[$_SERVER['PATH_INFO']]);
        halt($this->params);

        // 控制器初始化
        static::init();
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
}
