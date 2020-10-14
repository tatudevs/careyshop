<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    折扣商品模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/4
 */

namespace app\common\model;

class DiscountGoods extends CareyShop
{
    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'id',
        'discount_id',
    ];

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'discount_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'discount_id' => 'integer',
        'goods_id'    => 'integer',
        'discount'    => 'float',
    ];

    /**
     * hasOne cs_goods
     * @access public
     * @return mixed
     */
    public function getGoods()
    {
        return $this->hasOne(Goods::class, 'goods_id', 'goods_id');
    }

    /**
     * 添加折扣商品
     * @access public
     * @param array $discountGoods 商品数据
     * @param int   $discountId    折扣编号
     * @return array|false
     * @throws
     */
    public function addDiscountGoods(array $discountGoods, int $discountId)
    {
        // 处理外部填入数据并进行验证
        foreach ($discountGoods as $key => $value) {
            if (!$this->validateData($discountGoods[$key])) {
                return false;
            }

            $discountGoods[$key]['discount_id'] = $discountId;
        }

        return $this->saveAll($discountGoods)->toArray();
    }

    /**
     * 根据商品编号获取折扣信息
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getDiscountGoodsInfo(array $data)
    {
        if (!$this->validateData($data, 'info')) {
            return false;
        }

        // 搜索条件
        $map[] = ['g.goods_id', 'in', $data['goods_id']];
        $map[] = ['d.begin_time', '<=', time()];
        $map[] = ['d.end_time', '>=', time()];
        $map[] = ['d.status', '=', 1];

        $field = 'd.name,d.type,g.goods_id,g.discount,';
        $field .= 'from_unixtime(d.begin_time) as begin_time,';
        $field .= 'from_unixtime(d.end_time) as end_time';

        return $this->alias('g')
            ->field($field)
            ->join('discount d', 'd.discount_id = g.discount_id')
            ->where($map)
            ->select()
            ->toArray();
    }

    /**
     * 根据编号获取折扣商品明细
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getDiscountGoodsList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map[] = ['discount_goods.discount_id', '=', $data['discount_id']];
        $map[] = ['getGoods.status', '=', 1];
        $map[] = ['getGoods.is_delete', '=', 0];
        $map[] = ['getGoods.store_qty', '>', 0];

        // 关联查询
        $with['getGoods'] = ['name', 'store_qty', 'sales_sum', 'attachment'];

        $result = $this->withJoin($with)
            ->where($map)
            ->select()
            ->toArray();

        self::keyToSnake(['getGoods'], $result);
        return $result;
    }
}
