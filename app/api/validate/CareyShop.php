<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    API基类验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/20
 */

namespace app\api\validate;

use think\Validate;

class CareyShop extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'appkey'     => 'integer|length:8',
        'token'      => 'max:100',
        'sign'       => 'min:32',
        'timestamp'  => 'checkTimestamp',
        'format'     => 'in:json,jsonp,xml',
        'version'    => 'max:10',
        'controller' => 'max:20',
        'method'     => 'max:100',
        'callback'   => 'max:255', // jsonp的返回方法
    ];

    /**
     * 批量调用验证场景
     * @return CareyShop
     */
    public function sceneBatch()
    {
        return $this->only(['version', 'controller', 'method'])
            ->append('version', 'require')
            ->append('controller', 'require')
            ->append('method', 'require');
    }

    /**
     * 验证时间戳是否在允许范围内
     * @access protected
     * @param string $value 验证数据
     * @return string|true
     */
    protected function checkTimestamp(string $value)
    {
        $timestamp = ctype_digit($value) ? (int)$value : strtotime($value);
        if (false === $timestamp) {
            return '无效的日期格式';
        }

        if ($timestamp > strtotime('+10 minute') || $timestamp < strtotime('-10 minute')) {
            return 'timestamp已过期';
        }

        return true;
    }
}
