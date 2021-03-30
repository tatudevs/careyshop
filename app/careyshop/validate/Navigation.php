<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    导航验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/5/7
 */

namespace app\careyshop\validate;

class Navigation extends CareyShop
{
    /**
     * 验证规则
     * @var mixed|string[]
     */
    protected $rule = [
        'navigation_id' => 'integer|gt:0',
        'name'          => 'require|max:100',
        'url'           => 'require|max:255',
        'target'        => 'in:_self,_blank',
        'image'         => 'max:512',
        'icon'          => 'max:64',
        'sort'          => 'integer|between:0,255',
        'status'        => 'in:0,1',
        'order_type'    => 'requireWith:order_field|in:asc,desc',
        'order_field'   => 'requireWith:order_type|in:navigation_id,name,target,sort,status',
    ];

    /**
     * 字段描述
     * @var mixed|string[]
     */
    protected $field = [
        'navigation_id' => '导航编号',
        'name'          => '导航名称',
        'url'           => '导航链接',
        'target'        => '打开方式',
        'image'         => '导航图片',
        'icon'          => '导航图标',
        'sort'          => '导航排序值',
        'status'        => '导航是否启用',
        'order_type'    => '排序方式',
        'order_field'   => '排序字段',
    ];

    /**
     * 场景规则
     * @var mixed|string[]
     */
    protected $scene = [
        'set'    => [
            'navigation_id' => 'require|integer|gt:0',
            'name',
            'url',
            'target',
            'image',
            'icon',
            'sort',
            'status',
        ],
        'del'    => [
            'navigation_id' => 'require|arrayHasOnlyInts',
        ],
        'item'   => [
            'navigation_id' => 'require|integer|gt:0',
        ],
        'list'   => [
            'name' => 'max:100',
            'status',
            'order_type',
            'order_field',
        ],
        'target' => [
            'navigation_id' => 'require|arrayHasOnlyInts',
            'target'        => 'require|in:_self,_blank',
        ],
        'status' => [
            'navigation_id' => 'require|arrayHasOnlyInts',
            'status'        => 'require|in:0,1',
        ],
        'sort'   => [
            'navigation_id' => 'require|integer|gt:0',
            'sort'          => 'require|integer|between:0,255',
        ],
        'index'  => [
            'navigation_id' => 'require|arrayHasOnlyInts',
        ],
    ];
}
