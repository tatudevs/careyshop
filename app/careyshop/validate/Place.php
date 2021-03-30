<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    渠道平台验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/13
 */

namespace app\careyshop\validate;

class Place extends CareyShop
{
    /**
     * 验证规则
     * @var mixed|string[]
     */
    protected $rule = [
        'place_id'    => 'integer|gt:0',
        'name'        => 'require|max:30|unique:place,name,0,place_id',
        'code'        => 'integer|max:8',
        'platform'    => 'require|max:50',
        'model'       => 'require|max:50',
        'setting'     => 'require|array',
        'expand'      => 'array',
        'remark'      => 'max:255',
        'status'      => 'in:0,1',
        'page_no'     => 'integer|gt:0',
        'page_size'   => 'integer|gt:0',
        'order_type'  => 'requireWith:order_field|in:asc,desc',
        'order_field' => 'requireWith:order_type|in:place_id,name,platform,status',
    ];

    /**
     * 字段描述
     * @var mixed|string[]
     */
    protected $field = [
        'place_id'    => '渠道平台编号',
        'name'        => '渠道平台名称',
        'code'        => '渠道平台识别码',
        'platform'    => '所属平台',
        'model'       => '所属模块',
        'setting'     => '渠道平台配置',
        'expand'      => '渠道平台扩展',
        'remark'      => '渠道平台备注',
        'status'      => '渠道平台状态',
        'page_no'     => '页码',
        'page_size'   => '每页数量',
        'order_type'  => '排序方式',
        'order_field' => '排序字段',
    ];

    /**
     * 场景规则
     * @var mixed|string[]
     */
    protected $scene = [
        'setting' => [
            'platform',
            'model',
            'code',
        ],
        'set'     => [
            'place_id' => 'require|integer|gt:0',
            'name'     => 'max:30',
            'setting',
            'expand',
            'remark',
            'status',
        ],
        'item'    => [
            'place_id' => 'require|integer|gt:0',
        ],
        'list'    => [
            'name'     => 'max:30',
            'code',
            'platform' => 'max:50',
            'status',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
        'del'     => [
            'place_id' => 'require|arrayHasOnlyInts',
        ],
        'status'  => [
            'place_id' => 'require|arrayHasOnlyInts',
            'status'   => 'require|in:0,1',
        ],
    ];
}
