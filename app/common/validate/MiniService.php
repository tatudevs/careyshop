<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    微服务验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/13
 */

namespace app\common\validate;

class MiniService extends CareyShop
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'mini_service_id' => 'integer|gt:0',
        'name'            => 'require|max:30|unique:mini_service,name,0,mini_service_id',
        'code'            => 'integer|max:8',
        'platform'        => 'require|max:50',
        'model'           => 'require|max:50',
        'setting'         => 'require|array',
        'expand'          => 'array',
        'remark'          => 'max:255',
        'status'          => 'in:0,1',
        'page_no'         => 'integer|gt:0',
        'page_size'       => 'integer|gt:0',
        'order_type'      => 'requireWith:order_field|in:asc,desc',
        'order_field'     => 'requireWith:order_type|in:mini_service_id,name,platform,status',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'mini_service_id' => '微服务编号',
        'name'            => '微服务名称',
        'code'            => '微服务识别码',
        'platform'        => '所属平台',
        'model'           => '所属模块',
        'setting'         => '微服务配置',
        'expand'          => '微服务扩展',
        'remark'          => '微服务备注',
        'status'          => '微服务状态',
        'page_no'         => '页码',
        'page_size'       => '每页数量',
        'order_type'      => '排序方式',
        'order_field'     => '排序字段',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'setting' => [
            'platform',
            'model',
            'code',
        ],
        'set'     => [
            'mini_service_id' => 'require|integer|gt:0',
            'name'            => 'max:30',
            'setting',
            'expand',
            'remark',
            'status',
        ],
        'item'    => [
            'mini_service_id' => 'require|integer|gt:0',
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
            'mini_service_id' => 'require|arrayHasOnlyInts',
        ],
        'status'  => [
            'mini_service_id' => 'require|arrayHasOnlyInts',
            'status'          => 'require|in:0,1',
        ],
    ];
}
