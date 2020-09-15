<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    API路由配置文件
 *
 * @author      zxm <252404501@qq.com>
 * @version     v1.1
 * @date        2020/7/20
 */

use think\facade\Route;
use think\facade\Db;

$header['Access-Control-Expose-Headers'] = '*';
$header['Access-Control-Max-Age'] = '86400'; // 1天

$origin = Db::name('setting')
    ->where('module', '=', 'system_info')
    ->where('code', '=', 'allow_origin')
    ->cache(true, null, 'setting')
    ->value('value', []);

// 不允许全部通行时则根据规则生成域名
$allow = @json_decode($origin, true);
if (!in_array('*', $allow)) {
    $header['Access-Control-Allow-Origin'] = implode(',', $allow);
}

Route::rule(':version/:controller', ':version.:controller/index')
    ->allowCrossDomain($header);
