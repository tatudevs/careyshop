<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    外部请求参数容器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/21
 */

namespace app\common\wechat;

use ArrayAccess;

class Params implements ArrayAccess
{
    /**
     * 请求数据
     * @var array
     */
    protected $data = [];

    /**
     * Params constructor.
     * @access public
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset): ?string
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : '';
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * 替换数据源
     * @access public
     * @param array $data
     * @return $this
     */
    public function replace(array $data): Params
    {
        $this->data = $data;
        return $this;
    }

    /**
     * 获取原始数据
     * @access public
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
