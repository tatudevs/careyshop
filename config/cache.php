<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    缓存设置
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/6/6
 */

return [
    // 默认缓存驱动
    'default' => env('cache.driver', 'file'),

    // 缓存连接方式配置
    'stores'  => [
        // 框架默认缓存
        'file'  => [
            // 驱动方式
            'type'       => 'File',
            // 缓存保存目录
            'path'       => '',
            // 缓存前缀
            'prefix'     => '',
            // 缓存有效期 0表示永久缓存
            'expire'     => 0,
            // 缓存标签前缀
            'tag_prefix' => 'tag:',
            // 序列化机制 例如 ['serialize', 'unserialize']
            'serialize'  => [],
        ],
        // 第三方本地数据(如WeChat,可根据需要修改驱动方式)
        'place' => [
            // 驱动方式(需要数据持久化)
            'type'       => 'File',
            // 缓存保存目录
            'path'       => app()->getRuntimePath() . 'place' . DIRECTORY_SEPARATOR,
            // 缓存前缀
            'prefix'     => '',
            // 缓存有效期 0表示永久缓存
            'expire'     => 0,
            // 缓存标签前缀
            'tag_prefix' => 'tag:',
            // 序列化机制 例如 ['serialize', 'unserialize']
            'serialize'  => [],
        ],
        // Redis缓存
        'redis' => [
            // 驱动方式
            'type'       => 'Redis',
            // 服务器地址
            'host'       => '127.0.0.1',
            // 端口
            'port'       => 6379,
            // 密码
            'password'   => '',
            // 数据库索引号
            'select'     => 0,
            // 连接超时
            'timeout'    => 0,
            // 缓存有效期 0表示永久缓存
            'expire'     => 0,
            // 是否持久化
            'persistent' => false,
            // 缓存前缀
            'prefix'     => '',
            // 缓存标签前缀
            'tag_prefix' => 'tag:',
            // 序列化机制
            'serialize'  => [],
        ]
        // 更多的缓存连接
    ],
];
