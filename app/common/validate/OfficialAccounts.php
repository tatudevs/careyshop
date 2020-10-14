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
        'model'                => 'require|max:50|checkModule:official',
        'remark'               => 'max:255',
        'setting'              => 'require|array',
        'status'               => 'in:0,1',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'official_accounts_id' => '公众号编号',
        'name'                 => '公众号名称',
        'model'                => '所属模块',
        'remark'               => '公众号备注',
        'setting'              => '公众号配置',
        'status'               => '公众号状态',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'set'  => [
            'official_accounts_id' => 'require|integer|gt:0',
            'name'                 => 'require|max:30',
            'remark',
            'setting',
            'status',
        ],
        'item' => [
            'official_accounts_id' => 'require|integer|gt:0',
        ],
    ];
}
