<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    规则验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2018/3/27
 */

namespace app\careyshop\validate;

class AuthRule extends CareyShop
{
    /**
     * 验证规则
     * @var string[]
     */
    protected $rule = [
        'rule_id'     => 'integer|gt:0',
        'module'      => 'require|checkModule:module',
        'group_id'    => 'require|integer|gt:0',
        'name'        => 'require|max:32',
        'menu_auth'   => 'arrayHasOnlyInts',
        'log_auth'    => 'arrayHasOnlyInts',
        'sort'        => 'integer|between:0,255',
        'status'      => 'in:0,1',
        'order_type'  => 'requireWith:order_field|in:asc,desc',
        'order_field' => 'requireWith:order_type|in:rule_id,name,sort,status',
    ];

    /**
     * 字段描述
     * @var string[]
     */
    protected $field = [
        'rule_id'     => '规则编号',
        'module'      => '所属模块',
        'group_id'    => '用户组编号',
        'name'        => '规则名称',
        'menu_auth'   => '菜单权限',
        'log_auth'    => '记录权限',
        'sort'        => '规则排序值',
        'status'      => '规则状态',
        'order_type'  => '排序方式',
        'order_field' => '排序字段',
    ];

    /**
     * 场景规则
     * @var string[]
     */
    protected $scene = [
        'set'    => [
            'rule_id' => 'require|integer|gt:0',
            'name',
            'menu_auth',
            'log_auth',
            'sort',
            'status',
        ],
        'get'    => [
            'rule_id' => 'require|integer|gt:0',
        ],
        'del'    => [
            'rule_id' => 'require|arrayHasOnlyInts',
        ],
        'list'   => [
            'module'   => 'checkModule:module',
            'group_id' => 'integer|gt:0',
            'status',
            'order_type',
            'order_field',
        ],
        'status' => [
            'rule_id' => 'require|arrayHasOnlyInts',
            'status'  => 'require|in:0,1',
        ],
        'sort'   => [
            'rule_id' => 'require|integer|gt:0',
            'sort'    => 'require|integer|between:0,255',
        ],
        'index'  => [
            'rule_id' => 'require|arrayHasOnlyInts',
        ],
    ];
}
