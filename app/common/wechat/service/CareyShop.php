<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    WeChat 服务层基类
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/19
 */

namespace app\common\wechat\service;

use app\common\wechat\Params;
use app\common\wechat\WeChat;

class CareyShop
{
    /**
     * 控制器版本号
     * @var string
     */
    public $version = 'v1';

    /**
     * 错误信息
     * @var string
     */
    public $error = '';

    /**
     * WeChat 实列
     * @var mixed|null
     */
    public $app = null;

    /**
     * 请求参数容器
     * @var mixed
     */
    public $params;

    /**
     * CareyShop constructor.
     * @access public
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->initWeChat($params);
    }

    /**
     * 实际创建 WeChat 实列
     * @access public
     * @param array $params 请求参数
     * @return $this
     */
    public function initWeChat(array $params)
    {
        $this->params = new Params($params);
        if ($this->validateData()) {
            $wechat = new WeChat($this->params['code']);
            $this->app = $wechat->getApp();
        }

        return $this;
    }

//    /**
//     * 请求参数验证
//     * @access public
//     * @return bool
//     * @throws
//     */
//    public function validateData()
//    {
//        return validate([
//            'code|微服务识别码' => 'require|integer|max:8',
//        ])->check((array)$this->params);
//    }

    /**
     * 设置错误信息
     * @access public
     * @param string $value 错误信息
     * @return false
     */
    public function setError(string $value)
    {
        $this->error = $value;
        return false;
    }

    /*
     * 获取错误信息
     * @access public
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
}
