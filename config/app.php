<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    应用设置
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/6/6
 */

return [
    // 应用地址
    'app_host'                => env('app.host', ''),
    // 应用的命名空间
    'app_namespace'           => '',
    // 是否启用路由
    'with_route'              => true,
    // 是否启用事件
    'with_event'              => true,
    // 开启应用快速访问
    'app_express'             => true,
    // 默认应用
    'default_app'             => 'index',
    // 默认时区
    'default_timezone'        => 'Asia/Shanghai',

    // 应用映射（自动多应用模式有效）
    'app_map'                 => [],
    // 域名绑定（自动多应用模式有效）
    'domain_bind'             => [],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list'           => ['common', 'service', 'install'],

    // 异常页面的模板文件
    'exception_tmpl'          => app()->getRootPath() . 'public/template/exception.tpl',
    // 异常状态页面的模板文件
    'http_exception_template' => [
        // 定义404错误的重定向页面地址
        404 => app()->getRootPath() . 'public/template/404.tpl',
        // 还可以定义其它的HTTP status
        500 => app()->getRootPath() . 'public/template/50x.tpl',
        503 => app()->getRootPath() . 'public/template/50x.tpl',
        505 => app()->getRootPath() . 'public/template/50x.tpl',
    ],

    // 错误显示信息,非调试模式有效
    'error_message'           => '您访问的站点服务离线，请稍后访问！',
    // 显示错误信息
    'show_error_msg'          => false,

    // 默认每页显示条数
    'list_rows'               => env('paginate.list_rows', 15),
];
