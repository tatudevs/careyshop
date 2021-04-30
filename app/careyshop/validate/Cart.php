<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    购物车验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/7/12
 */

namespace app\careyshop\validate;

class Cart extends CareyShop
{
    /**
     * 验证规则
     * @var string[]
     */
    protected $rule = [
        'cart_id'     => 'integer|gt:0',
        'goods_id'    => 'require|integer|gt:0',
        'goods_spec'  => 'arrayHasOnlyInts',
        'goods_num'   => 'require|integer|gt:0',
        'is_selected' => 'in:0,1',
        'show_size'   => 'egt:0',
        'cart_goods'  => 'array',
        'total_type'  => 'in:goods,number',
        'former_spec' => 'max:50',
        'client_id'   => 'integer|gt:0',
    ];

    /**
     * 字段描述
     * @var string[]
     */
    protected $field = [
        'cart_id'     => '购物车编号',
        'goods_id'    => '商品编号',
        'goods_spec'  => '商品规格',
        'goods_num'   => '购买数量',
        'is_selected' => '是否选中',
        'show_size'   => '显示多少记录',
        'cart_goods'  => '购物车商品列表',
        'total_type'  => '合计类型',
        'former_spec' => '原先的商品规格',
        'client_id'   => '账号编号',
    ];

    /**
     * 场景规则
     * @var string[]
     */
    protected $scene = [
        'list'   => [
            'show_size',
            'client_id' => 'require|integer|gt:0',
        ],
        'select' => [
            'cart_id'     => 'require|arrayHasOnlyInts',
            'is_selected' => 'in:0,1',
        ],
        'del'    => [
            'cart_id' => 'require|arrayHasOnlyInts',
        ],
        'add'    => [
            'cart_goods' => 'require|array',
        ],
        'total'  => [
            'total_type' => 'require|in:goods,number',
            'client_id'  => 'require|integer|gt:0',
        ],
    ];
}
