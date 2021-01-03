<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    订单商品模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/31
 */

namespace app\common\model;

use app\common\validate\Order as Validate;

class OrderGoods extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'order_goods_id';

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'order_goods_id',
        'order_id',
        'order_no',
        'user_id',
        'key_name',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'order_goods_id' => 'integer',
        'order_id'       => 'integer',
        'goods_id'       => 'integer',
        'user_id'        => 'integer',
        'market_price'   => 'float',
        'shop_price'     => 'float',
        'qty'            => 'integer',
        'is_comment'     => 'integer',
        'is_service'     => 'integer',
        'status'         => 'integer',
    ];

    /**
     * belongsTo cs_order
     * @access public
     * @return mixed
     */
    public function toOrder()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * belongsTo cs_order
     * @access public
     * @return mixed
     */
    public function getOrder()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * hasMany cs_order_goods
     * @access public
     * @return mixed
     */
    public function getOrderGoods()
    {
        return $this->hasMany(OrderGoods::class, 'order_id', 'order_id');
    }

    /**
     * 获取指定商品编号已购买的数量
     * @access public
     * @param int $goodsId 商品编号
     * @return int
     */
    public static function getBoughtGoods(int $goodsId): int
    {
        // 搜索条件
        $map[] = ['g.user_id', '=', get_client_id()];
        $map[] = ['g.goods_id', '=', $goodsId];
        $map[] = ['g.status', '<>', 3];
        $map[] = ['o.trade_status', '<>', 4];

        return self::alias('g')
            ->join('order o', 'o.order_id = g.order_id')
            ->where($map)
            ->sum('g.qty');
    }

    /**
     * 判断订单商品是否允许评价
     * @access public
     * @param string $orderNo      订单号
     * @param int    $orderGoodsId 订单商品编号
     * @return bool
     * @throws
     */
    public function isComment(string $orderNo, int $orderGoodsId): bool
    {
        // 搜索条件
        $map[] = ['order_goods_id', '=', $orderGoodsId];
        $map[] = ['order_no', '=', $orderNo];
        $map[] = ['user_id', '=', get_client_id()];

        // 关联查询
        $with['to_order'] = function ($query) {
            $query->field('order_id,trade_status')->where('is_delete', '=', 0);
        };

        // 获取关联订单数据
        $result = $this
            ->field('order_id,is_comment,status')
            ->with($with)
            ->where($map)
            ->find();

        if (is_null($result)) {
            return $this->setError('订单或订单商品不存在');
        }

        if ($result->getAttr('is_comment') === 3) {
            return $this->setError('该订单商品不可评价');
        }

        if ($result->getAttr('is_comment') !== 0) {
            return $this->setError('该订单商品已评价');
        }

        if ($result->getAttr('status') !== 2 || $result->getAttr('to_order')->getAttr('trade_status') !== 3) {
            return $this->setError('该订单商品状态不允许评价');
        }

        return true;
    }

    /**
     * 获取一个订单商品明细
     * @access public
     * @param array $data          外部数据
     * @param bool  $returnArray   是否以数组的形式返回
     * @param bool  $hasOrderGoods 是否关联订单数据
     * @return false|array|object
     * @throws
     */
    public function getOrderGoodsItem(array $data, $returnArray = true, $hasOrderGoods = true)
    {
        if (!$this->validateData($data, 'goods_item', false, Validate::class)) {
            return false;
        }

        // 搜索条件
        $map[] = ['order_goods.order_goods_id', '=', $data['order_goods_id']];
        is_client_admin() ?: $map[] = ['order_goods.user_id', '=', get_client_id()];

        // 关联查询
        $with = [];
        if ($hasOrderGoods) {
            $with = ['getOrder'];
            $map[] = ['getOrder.is_delete', '<>', 2];
        }

        // 实际查询
        $result = $this
            ->alias('order_goods')
            ->withJoin($with)
            ->where($map)
            ->find();

        if (is_null($result)) {
            return $this->setError('订单商品不存在');
        }

        // 返回数据
        if ($returnArray) {
            // 隐藏不需要输出的字段
            $hidden = [
                'order_id', 'getOrder.order_id', 'getOrder.parent_id',
                'getOrder.order_no', 'getOrder.user_id', 'getOrder.create_user_id',
            ];

            $temp = [$result->hidden($hidden)->toArray()];
            self::keyToSnake(['getOrder'], $temp);

            return $temp[0];
        }

        return $result;
    }
}
