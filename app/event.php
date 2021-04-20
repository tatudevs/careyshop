<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    全局事件定义文件
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/20
 */

return [
    // 绑定事件
    'bind'      => [
    ],

    // 监听事件
    'listen'    => [
        'AppInit'  => [],
        'HttpRun'  => [],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [],
    ],

    // 订阅事件
    'subscribe' => [
        \app\careyshop\event\subscribe\Ask::class,
        \app\careyshop\event\subscribe\Card::class,
        \app\careyshop\event\subscribe\Goods::class,
        \app\careyshop\event\subscribe\Invoice::class,
        \app\careyshop\event\subscribe\Order::class,
        \app\careyshop\event\subscribe\Service::class,
        \app\careyshop\event\subscribe\User::class,
        \app\careyshop\event\subscribe\Withdraw::class,
    ],
];
