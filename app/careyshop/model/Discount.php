<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品折扣模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/21
 */

namespace app\careyshop\model;

class Discount extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'discount_id';

    /**
     * 只读属性
     * @var mixed|string[]
     */
    protected $readonly = [
        'discount_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var mixed|string[]
     */
    protected $type = [
        'discount_id' => 'integer',
        'type'        => 'integer',
        'begin_time'  => 'timestamp',
        'end_time'    => 'timestamp',
        'status'      => 'integer',
    ];

    /**
     * hasMany cs_discount_goods
     * @access public
     * @return mixed
     */
    public function discountGoods()
    {
        return $this->hasMany(DiscountGoods::class, 'discount_id');
    }

    /**
     * 检测相同时间段内是否存在重复商品
     * @access private
     * @param string $beginTime 开始日期
     * @param string $endTime   结束日期
     * @param array  $goodsList 外部商品列表
     * @param int    $excludeId 排除折扣Id
     * @return bool
     * @throws
     */
    private function isRepeatGoods(string $beginTime, string $endTime, array $goodsList, $excludeId = 0): bool
    {
        $map = [];
        $excludeId == 0 ?: $map[] = ['discount_id', '<>', $excludeId];
        $map[] = ['begin_time', '< time', $endTime];
        $map[] = ['end_time', '> time', $beginTime];

        // 获取相同日期范围内的商品
        $result = $this->with('discount_goods')->where($map)->select();
        foreach ($result as $value) {
            $discountGoods = $value->getAttr('discount_goods')->column('goods_id');
            $inGoods = array_intersect($discountGoods, $goodsList);

            if (!empty($inGoods)) {
                $error = '商品Id:' . implode(',', $inGoods) . ' 已在同时间段的"';
                $error .= $value->getAttr('name') . '(Id:' . $value->getAttr('discount_id') . ')"中存在';
                return $this->setError($error);
            }
        }

        return true;
    }

    /**
     * 添加一个商品折扣
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addDiscountItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['discount_id']);

        // 检测相同时间段内是否存在重复商品
        $goodsList = array_column($data['discount_goods'], 'goods_id');
        if (!$this->isRepeatGoods($data['begin_time'], $data['end_time'], $goodsList)) {
            return false;
        }

        // 开启事务
        $this->startTrans();

        try {
            // 添加主表
            $this->save($data);
            $result = $this->toArray();

            // 添加折扣商品
            $discountGoodsDb = new DiscountGoods();
            $result['discount_goods'] = $discountGoodsDb->addDiscountGoods($data['discount_goods'], $this->getAttr('discount_id'));

            if (false === $result['discount_goods']) {
                throw new \Exception($discountGoodsDb->getError());
            }

            $this->commit();
            return $result;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 编辑一个商品折扣
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setDiscountItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        // 检测相同时间段内是否存在重复商品
        $goodsList = array_column($data['discount_goods'], 'goods_id');
        if (!$this->isRepeatGoods($data['begin_time'], $data['end_time'], $goodsList, $data['discount_id'])) {
            return false;
        }

        // 开启事务
        $this->startTrans();

        try {
            // 修改主表
            $map[] = ['discount_id', '=', $data['discount_id']];
            $temp = self::update($data, $map);

            // 删除关联数据
            $discountGoodsDb = new DiscountGoods();
            $discountGoodsDb->where($map)->delete();

            // 添加折扣商品
            $result = $temp->toArray();
            $result['discount_goods'] = $discountGoodsDb->addDiscountGoods($data['discount_goods'], $data['discount_id']);

            if (false === $result['discount_goods']) {
                throw new \Exception($discountGoodsDb->getError());
            }

            $this->commit();
            return $result;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 获取一个商品折扣
     * @access public
     * @param array $data 外部数据
     * @return array|false|null
     * @throws
     */
    public function getDiscountItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $result = $this->with('discount_goods')->find($data['discount_id']);
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 批量删除商品折扣
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delDiscountList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        // 搜索条件
        $map[] = ['discount_id', 'in', $data['discount_id']];

        $this->where($map)->delete();
        DiscountGoods::where($map)->delete();

        return true;
    }

    /**
     * 批量设置商品折扣状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setDiscountStatus(array $data): bool
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['discount_id', 'in', $data['discount_id']];
        self::update(['status' => $data['status']], $map);

        return true;
    }

    /**
     * 获取商品折扣列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getDiscountList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['name']) ?: $map[] = ['name', 'like', '%' . $data['name'] . '%'];
        empty($data['type']) ?: $map[] = ['type', 'in', $data['type']];
        empty($data['begin_time']) ?: $map[] = ['begin_time', '< time', $data['end_time']];
        empty($data['end_time']) ?: $map[] = ['end_time', '> time', $data['begin_time']];
        is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['discount_id' => 'desc'])
            ->with('discount_goods')
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        return $result;
    }
}
