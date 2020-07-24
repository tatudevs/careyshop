<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    服务层基类
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/21
 */

namespace app\common\service;

class CareyShop
{
    /**
     * 错误信息
     * @var string
     */
    public $error;

    /**
     * 设置错误信息并抛出异常
     * @access public
     * @param string $value 错误信息
     * @throws \Exception
     */
    public function setError($value)
    {
        $this->error = $value;
        throw new \Exception($value);
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
