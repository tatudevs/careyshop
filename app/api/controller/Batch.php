<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    API批量调用
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/21
 */

namespace app\api\controller;

use Exception;
use think\helper\Str;

class Batch extends CareyShop
{
    /**
     * API批量调用首页
     * @access public
     * @return array
     */
    public function index()
    {
        // 删除多余数据,避免影响其他模块
        unset($this->params['appkey']);
        unset($this->params['token']);
        unset($this->params['timestamp']);
        unset($this->params['format']);
        unset($this->params['method']);

        // 字段不存在时直接返回
        if (!isset($this->params['batch']) || !is_array($this->params['batch'])) {
            $this->outputError('batch参数与规则不符');
        }

        $result = [];
        foreach ($this->params['batch'] as $key => $value) {
            isset($value['version']) ?: $value['version'] = '';
            isset($value['controller']) ?: $value['controller'] = '';
            isset($value['method']) ?: $value['method'] = '';

            // 为生成控制器与模型对象准备数据
            $version = Str::lower($value['version']);
            $controller = Str::studly($value['controller']);
            $method = $value['method'];

            $oldData['version'] = $value['version'];
            $oldData['controller'] = $value['controller'];
            $oldData['method'] = $value['method'];
            $oldData['class'] = sprintf('app\\api\\controller\\%s\\%s', $version, $controller);

            $callback = null;
            static::$model = null;

            // 此处的$controller值必须使用源值,否则大小写匹配不上
            $authUrl = sprintf('%s/%s/%s/%s', app('http')->getName(), $version, $value['controller'], $method);

            try {
                // 验证数据
                $validate = $this->validate($value, 'CareyShop.batch');
                if (true !== $validate) {
                    throw new Exception($validate);
                }

                // 权限验证,先验证是否属于白名单,再验证是否有权限
                if (!$this->apiDebug) {
                    if (!static::$auth->checkWhite($authUrl)) {
                        if (!static::$auth->check($authUrl)) {
                            throw new Exception('权限不足', 403);
                        }
                    }
                }

                $route = [];
                $oldData['class']::initMethod();

                if (!array_key_exists($method, $oldData['class']::$route)) {
                    throw new Exception('method路由方法不存在');
                }

                // 获取实际调用方法
                $method = $oldData['class']::$route[$method];

                // 检测是否指定指向类
                if (!isset($method[1])) {
                    $method[1] = 'app\\common\\model\\' . $controller;
                }

                // 实例化指向类
                if (class_exists($method[1])) {
                    static::$model = new $method[1];
                } else if (method_exists($oldData['class'], $method[0])) {
                    static::$model = new $oldData['class']($this->app);
                } else {
                    throw new Exception('method不支持批量调用');
                }

                if (!method_exists(static::$model, $method[0])) {
                    throw new Exception('method成员方法不存在');
                }

                unset($value['version'], $value['controller'], $value['method']);
                $callback = call_user_func([static::$model, $method[0]], $value);
            } catch (Exception $e) {
                $callback = false;
                $this->setError($e->getMessage());
            }

            // 确定调用结果
            if (false === $callback) {
                !empty($this->error) ?: $this->setError(static::$model->getError());
            }

            $result[$key] = [
                'status'     => false !== $callback ? 200 : 500,
                'message'    => false !== $callback ? 'success' : $this->getError(),
                'version'    => $oldData['version'],
                'controller' => $oldData['controller'],
                'method'     => $oldData['method'],
                'data'       => $callback,
            ];

            // 日志记录
            static::$auth->saveLog(
                $authUrl,
                $this->request,
                false !== $callback ? $result[$key] : false,
                $oldData['class'],
                $this->getError()
            );
        }

        if (empty($result)) {
            $this->outputError('请求结果为空');
        }

        return $this->outputResult($result);
    }
}
