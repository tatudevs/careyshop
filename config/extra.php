<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    扩展配置
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/20
 */

return [
    // 产品信息
    'product'      => [
        'product_name'    => 'CareyShop商城框架系统',
        'product_version' => '1.3.2',
        'build_version'   => '20201012',
        'product_website' => 'http://www.careyshop.cn',
        'product_update'  => 'http://www.careyshop.cn/checkUpdate',
        'develop_team'    => 'think',
        'company_name'    => '宁波互明科技有限公司',
        'company_website' => 'http://www.careyshop.cn',
    ],

    // 应用模块
    'module_group' => [
        'api'   => 'API',
        'admin' => '后台',
        'home'  => '前台',
    ],

    // 用户组
    'client_group' => [
        'visitor' => [
            'value' => -1,
            'name'  => '游客组',
        ],
        'user'    => [
            'value' => 0,
            'name'  => '顾客组',
        ],
        'admin'   => [
            'value' => 1,
            'name'  => '管理组',
        ],
    ],

    // 渠道平台
    'place'        => [
        'wechat' => [
            'name'  => '微信',
            'model' => [
                'official_account' => '公众号',
            ],
        ],

        // 您可以扩展更多
    ],
];
