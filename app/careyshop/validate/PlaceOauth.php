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

class PlaceOauth extends CareyShop
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'place_oauth_id' => 'integer|gt:0',
        'name'           => 'require|max:16',
        'model'          => 'require|max:16|checkModule:oauth',
        'channel'        => 'max:16',
        'client_id'      => 'require|max:32',
        'client_secret'  => 'require|max:128',
        'expand'         => 'min:0',
        'logo'           => 'max:512',
        'icon'           => 'max:64',
        'status'         => 'in:0,1',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'place_oauth_id' => '授权机制编号',
        'name'           => '授权名称',
        'model'          => '所属模块',
        'channel'        => '对应渠道',
        'client_id'      => 'ClientId',
        'client_secret'  => 'ClientSecret',
        'expand'         => '扩展配置',
        'logo'           => '授权商商标',
        'icon'           => '授权商图标',
        'status'         => '授权状态',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'set'       => [
            'place_oauth_id' => 'require|integer|gt:0',
            'name',
            'model',
            'channel',
            'client_id',
            'client_secret',
            'expand',
            'logo',
            'icon',
            'status',
        ],
        'del'       => [
            'place_oauth_id' => 'require|arrayHasOnlyInts',
        ],
        'item'      => [
            'place_oauth_id' => 'require|integer|gt:0',
        ],
        'list'      => [
            'name'   => 'max:16',
            'model'  => 'max:16|checkModule:oauth',
            'channel',
            'status' => 'in:0,1',
        ],
        'type'      => [
            'channel',
        ],
        'status'    => [
            'place_oauth_id' => 'require|arrayHasOnlyInts',
            'status'         => 'require|in:0,1',
        ],
        'authorize' => [
            'model',
            'channel',
        ],
    ];
}
