<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    WeChat 配置类
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/19
 */

namespace app\common\wechat;

use ArrayAccess;

class Config implements ArrayAccess
{
    /**
     * 默认配置,将合并到"$setting"
     * @var array
     */
    protected $default = [
        // 指定 API 调用返回结果的类型：array(default)/object/raw/自定义类名
        'response_type' => 'array',
        /**
         * 日志配置
         *
         * level: 日志级别, 可选为：debug/info/notice/warning/error/critical/alert/emergency
         * permission：日志文件权限(可选)，默认为null（若为null值,monolog会取0644）
         * file：日志文件位置(绝对路径!!!)，要求可写权限
         */
        'log'           => [
            'level'      => 'debug',
            'permission' => 0777,
            'file'       => 'runtime/log/wechat.log',
        ],
    ];

    /**
     * 配置数据
     * @var array
     */
    protected $setting = [];

    /**
     * 是否使用自定义的缓存系统
     * @var bool
     */
    protected $useCache = true;

    public function __construct()
    {
    }

    /**
     * 检测是否存在配置参数
     * @access public
     * @param string $key 键名
     * @return bool
     */
    public function has(string $key)
    {
        return array_key_exists($key, $this->setting);
    }

    // ArrayAccess
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->has($offset) ? $this->setting[$offset] : '';
    }

    public function offsetSet($offset, $value)
    {
        $this->setting[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->setting[$offset]);
    }
}
