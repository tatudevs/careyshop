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

    /**
     * 数据验证
     * @access public
     * @param array $data 待验证数据
     * @return bool
     */
    public function validateData(array $data)
    {
        // 验证规则
        $rule = [
            'code|微服务识别码' => 'require|integer|max:8',
        ];

        $v = validate($rule, [], false, false);
        if (!$v->check($data)) {
            return $this->setError($v->getError());
        }

        return true;
    }
}
