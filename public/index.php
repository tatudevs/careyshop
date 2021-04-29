<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    应用入口文件
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/20
 */

namespace think;

// PHP版本检查
if (version_compare(PHP_VERSION, '7.4', '<')) {
    header('Content-type: text/html; charset=utf-8');
    die('PHP版本过低，最少需要PHP7.4，请升级PHP版本！');
}

// 检测是否完成安装
if (!is_file(__DIR__ . '/../.env') || !is_file(__DIR__ . '/../public/static/install/install.lock')) {
    define('IS_INITIALIZE', false);
    $appName = 'install';
}

require __DIR__ . '/../vendor/autoload.php';

// 执行HTTP应用并响应
$http = (new App())->http;

$response = $http->name($appName ?? '')->run();

$response->send();

$http->end($response);
