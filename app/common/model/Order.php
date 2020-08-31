<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    订单管理模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/7/29
 */

namespace app\common\model;

class Order extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'order_id';

    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'parent_id',
        'create_user_id',
    ];

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'order_id',
        'parent_id',
        'order_no',
        'user_id',
        'create_user_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'order_id'        => 'integer',
        'parent_id'       => 'integer',
        'user_id'         => 'integer',
        'pay_amount'      => 'float',
        'goods_amount'    => 'float',
        'total_amount'    => 'float',
        'delivery_fee'    => 'float',
        'use_money'       => 'float',
        'use_level'       => 'float',
        'use_integral'    => 'float',
        'use_coupon'      => 'float',
        'use_discount'    => 'float',
        'use_promotion'   => 'float',
        'use_card'        => 'float',
        'integral_pct'    => 'float',
        'delivery_id'     => 'integer',
        'country'         => 'integer',
        'region_list'     => 'array',
        'invoice_type'    => 'integer',
        'invoice_amount'  => 'float',
        'trade_status'    => 'integer',
        'delivery_status' => 'integer',
        'payment_status'  => 'integer',
        'create_user_id'  => 'integer',
        'is_give'         => 'integer',
        'adjustment'      => 'float',
        'give_integral'   => 'integer',
        'give_coupon'     => 'array',
        'payment_time'    => 'timestamp',
        'picking_time'    => 'timestamp',
        'delivery_time'   => 'timestamp',
        'finished_time'   => 'timestamp',
        'is_delete'       => 'integer',
    ];

}
