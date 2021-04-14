<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    错误处理
 *
 * @author      zxm <252404501@qq.com>
 * @version     v1.1
 * @date        2021/4/14
 */

namespace app\careyshop\concern;

trait Error
{
    /**
     * 错误信息
     * @var string
     */
    protected string $error = '';

    /**
     * 返回模型的错误信息
     * @access public
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * 设置模型错误信息
     * @access public
     * @param string $value 错误信息
     * @return false
     */
    public function setError(string $value)
    {
        $this->error = $value;
        return false;
    }
}
