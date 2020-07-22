<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商城移动端控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/20
 */

declare (strict_types = 1);

namespace app\mobile\controller;

class Index
{
    public function index()
    {
        $vars = [
            'method' => 'get.qrcode.item',
            'text'   => urlencode('https://www.careyshop.cn'),
        ];

        $url = url('api/v1/qrcode', $vars, true, true)->build();
        return $url;
//        return '欢迎使用CareyShop商城框架系统 - Mobile';
    }
}
