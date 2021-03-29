<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    OAuth2.0验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/3/26
 */

namespace app\careyshop\validate;

class Oauth extends CareyShop
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'oauth_id'      => 'integer|gt:0',
        'name'          => 'require|max:16',
        'model'         => 'require|max:16|checkModule:oauth',
        'terminal'      => 'require|max:16',
        'client_id'     => 'require|max:32',
        'client_secret' => 'require|max:128',
        'expand'        => 'min:0',
        'logo'          => 'max:512',
        'icon'          => 'max:64',
        'status'        => 'in:0,1',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'oauth_id'      => '授权机制编号',
        'name'          => '授权名称',
        'model'         => '所属模块',
        'terminal'      => '终端平台',
        'client_id'     => 'AppID',
        'client_secret' => 'AppSecret',
        'expand'        => '扩展配置',
        'logo'          => '授权商商标',
        'icon'          => '授权商图标',
        'status'        => '授权状态',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'set'    => [
            'oauth_id' => 'require|integer|gt:0',
            'name',
            'model',
            'terminal',
            'client_id',
            'client_secret',
            'expand',
            'logo',
            'icon',
            'status',
        ],
        'del'    => [
            'oauth_id' => 'require|arrayHasOnlyInts',
        ],
        'item'   => [
            'oauth_id' => 'require|integer|gt:0',
        ],
        'list'   => [
            'name'     => 'max:16',
            'model'    => 'max:16|checkModule:oauth',
            'terminal' => 'max:16',
            'status'   => 'in:0,1',
        ],
        'type'   => [
            'terminal',
        ],
        'status' => [
            'oauth_id' => 'require|arrayHasOnlyInts',
            'status'   => 'require|in:0,1',
        ],
    ];
}
