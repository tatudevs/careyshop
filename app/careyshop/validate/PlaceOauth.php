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
     * @var mixed|string[]
     */
    protected $rule = [
        'place_oauth_id' => 'integer|gt:0',
        'name'           => 'require|max:16',
        'model'          => 'require|max:16|checkModule:oauth',
        'place_id'       => 'require|integer|gt:0',
        'client_id'      => 'require|max:32',
        'client_secret'  => 'require|max:128',
        'config'         => 'min:0',
        'expand'         => 'min:0',
        'logo'           => 'max:512',
        'icon'           => 'max:64',
        'status'         => 'in:0,1',
    ];

    /**
     * 字段描述
     * @var mixed|string[]
     */
    protected $field = [
        'place_oauth_id' => '授权机制编号',
        'name'           => '授权名称',
        'model'          => '所属模块',
        'place_id'       => '对应渠道',
        'client_id'      => 'ClientId',
        'client_secret'  => 'ClientSecret',
        'config'         => '扩展配置',
        'expand'         => '扩展参数',
        'logo'           => '授权商商标',
        'icon'           => '授权商图标',
        'status'         => '授权状态',
    ];

    /**
     * 场景规则
     * @var mixed|string[]
     */
    protected $scene = [
        'set'    => [
            'place_oauth_id' => 'require|integer|gt:0',
            'name',
            'model',
            'client_id',
            'client_secret',
            'config',
            'expand',
            'logo',
            'icon',
            'status',
        ],
        'del'    => [
            'place_oauth_id' => 'require|arrayHasOnlyInts',
        ],
        'item'   => [
            'place_oauth_id' => 'require|integer|gt:0',
        ],
        'list'   => [
            'name' => 'max:16',
            'place_id',
            'status',
        ],
        'status' => [
            'place_oauth_id' => 'require|arrayHasOnlyInts',
            'status'         => 'require|in:0,1',
        ],
        'oauth'  => [
            'place_oauth_id',
        ],
    ];
}
