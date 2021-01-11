<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    Cookie设置
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/6/6
 */

return [
    // cookie 保存时间
    'expire'    => 0,
    // cookie 保存路径
    'path'      => '/',
    // cookie 有效域名
    'domain'    => '',
    //  cookie 启用安全传输
    'secure'    => false,
    // httponly设置
    'httponly'  => false,
    // 是否使用 setcookie
    'setcookie' => true,
    // samesite 设置，支持 'strict' 'lax'
    'samesite'  => '',
];
