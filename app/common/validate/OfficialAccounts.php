<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    公众号验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/13
 */

namespace app\common\validate;

class OfficialAccounts extends CareyShop
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'official_accounts_id' => 'integer|gt:0',
        'name'                 => 'require|max:30|unique:official_accounts,name,0,official_accounts_id',
        'code'                 => 'integer|max:8',
        'model'                => 'require|max:50|checkModule:official',
        'remark'               => 'max:255',
        'setting'              => 'array',
        'status'               => 'in:0,1',
        'url'                  => 'url',
        'page_no'              => 'integer|gt:0',
        'page_size'            => 'integer|gt:0',
        'order_type'           => 'requireWith:order_field|in:asc,desc',
        'order_field'          => 'requireWith:order_type|in:official_accounts_id,name,model,status',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'official_accounts_id' => '公众号编号',
        'name'                 => '公众号名称',
        'code'                 => '公众号识别码',
        'model'                => '所属模块',
        'remark'               => '公众号备注',
        'setting'              => '公众号配置',
        'status'               => '公众号状态',
        'url'                  => 'URL',
        'page_no'              => '页码',
        'page_size'            => '每页数量',
        'order_type'           => '排序方式',
        'order_field'          => '排序字段',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'setting' => [
            'model',
            'code',
            'url',
        ],
        'set'     => [
            'official_accounts_id' => 'require|integer|gt:0',
            'name'                 => 'require|max:30',
            'remark',
            'setting',
            'status',
        ],
        'item'    => [
            'official_accounts_id' => 'require|integer|gt:0',
        ],
        'list'    => [
            'name'  => 'max:30',
            'model' => 'max:50|checkModule:official',
            'code',
            'status',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
        'del'     => [
            'official_accounts_id' => 'require|arrayHasOnlyInts',
        ],
        'status'  => [
            'official_accounts_id' => 'require|arrayHasOnlyInts',
            'status'               => 'require|in:0,1',
        ],
    ];
}
