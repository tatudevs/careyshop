<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    购物卡使用验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/11/21
 */

namespace app\common\validate;

class CardUse extends CareyShop
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'card_use_id'    => 'integer|gt:0',
        'card_id'        => 'integer|gt:0',
        'name'           => 'max:50',
        'number'         => 'length:11',
        'password'       => 'length:16',
        'is_invalid'     => 'in:0,1',
        'is_active'      => 'in:0,1',
        'remark'         => 'max:255',
        'src_number'     => 'length:11',
        'money'          => 'float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'exclude_number' => 'length:11',
        'type'           => 'in:normal,invalid',
        'account'        => 'max:80',
        'goods_id'       => 'arrayHasOnlyInts',
        'page_no'        => 'integer|gt:0',
        'page_size'      => 'integer|gt:0',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'card_use_id'    => '编号',
        'card_id'        => '购物卡编号',
        'name'           => '购物卡名称',
        'number'         => '卡号',
        'password'       => '卡密',
        'is_invalid'     => '是否有效',
        'is_active'      => '是否激活',
        'remark'         => '备注',
        'src_number'     => '被合并卡卡号',
        'money'          => '金额',
        'exclude_number' => '排除卡号',
        'type'           => '筛选类型',
        'account'        => '账号',
        'goods_id'       => '商品编号',
        'page_no'        => '页码',
        'page_size'      => '每页数量',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'bind'       => [
            'number'   => 'require|length:11',
            'password' => 'require|length:16',
        ],
        'invalid'    => [
            'card_use_id' => 'require|arrayHasOnlyInts',
            'is_invalid'  => 'require|in:0,1',
            'remark'      => 'require|max:255',
        ],
        'export'     => [
            'card_id' => 'require|integer|gt:0',
        ],
        'merge_list' => [
            'exclude_number',
        ],
        'merge'      => [
            'number'     => 'require|length:11',
            'src_number' => 'require|length:11',
            'money',
        ],
        'list'       => [
            'card_id' => 'integer|egt:0',
            'number',
            'name',
            'type',
            'account',
            'is_active',
            'page_no',
            'page_size',
        ],
        'select'     => [
            'goods_id' => 'require|arrayHasOnlyInts',
        ],
        'check'      => [
            'number'   => 'require|length:11',
            'goods_id' => 'require|arrayHasOnlyInts',
            'money'    => 'float|egt:0|regex:^\d+(\.\d{1,2})?$',
        ],
    ];
}
