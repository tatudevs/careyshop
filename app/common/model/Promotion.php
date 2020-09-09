<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    订单促销模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/23
 */

namespace app\common\model;

class Promotion extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'promotion_id';

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'promotion_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'promotion_id' => 'integer',
        'begin_time'   => 'timestamp',
        'end_time'     => 'timestamp',
        'status'       => 'integer',
    ];

    /**
     * hasMany cs_promotion_item
     * @access public
     * @return mixed
     */
    public function promotionItem()
    {
        return $this->hasMany(PromotionItem::class, 'promotion_id');
    }

    /**
     * 检测相同时间段内是否存在重复促销
     * @access private
     * @param string $beginTime 开始日期
     * @param string $endTime   结束日期
     * @param int    $excludeId 排除折扣Id
     * @return bool
     * @throws
     */
    private function isRepeatPromotion(string $beginTime, string $endTime, $excludeId = 0)
    {
        $map = [];
        $excludeId == 0 ?: $map[] = ['promotion_id', '<>', $excludeId];
        $map[] = ['begin_time', '< time', $endTime];
        $map[] = ['end_time', '> time', $beginTime];

        // 获取相同日期范围内的促销
        $result = $this->where($map)->find();
        if (!is_null($result)) {
            $error = sprintf('该时间段内已存在"%s(Id:%d)"', $result->getAttr('name'), $result->getAttr('promotion_id'));
            return $this->setError($error);
        }

        return true;
    }

    /**
     * 添加一个订单促销
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addPromotionItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关及处理部分数据
        unset($data['promotion_id']);

        if (!$this->isRepeatPromotion($data['begin_time'], $data['end_time'])) {
            return false;
        }

        // 开启事务
        $this->startTrans();

        try {
            // 添加主表
            $this->save($data);
            $result = $this->toArray();

            // 添加促销方案配置
            $promotionItemDb = new PromotionItem();
            $result['promotion_item'] = $promotionItemDb->addPromotionItem($data['promotion_item'], $this->getAttr('promotion_id'));

            if (false === $result['promotion_item']) {
                throw new \Exception($promotionItemDb->getError());
            }

            $this->commit();
            return $result;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 编辑一个订单促销
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setPromotionItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        if (isset($data['begin_time']) || isset($data['end_time'])) {
            if (!$this->isRepeatPromotion($data['begin_time'], $data['end_time'], $data['promotion_id'])) {
                return false;
            }
        }

        // 开启事务
        $this->startTrans();

        try {
            // 修改主表
            $map[] = ['promotion_id', '=', $data['promotion_id']];
            $temp = self::update($data, $map);

            // 获取主表数据
            $result = $temp->toArray();

            if (!empty($data['promotion_item'])) {
                // 删除关联数据
                $promotionItemDb = new PromotionItem();
                $promotionItemDb->where($map)->delete();

                // 添加促销方案配置
                $result['promotion_item'] = $promotionItemDb->addPromotionItem($data['promotion_item'], $data['promotion_id']);
                if (false === $result['promotion_item']) {
                    throw new \Exception($promotionItemDb->getError());
                }
            }

            $this->commit();
            return $result;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 获取一个订单促销
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getPromotionItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $result = $this->with('promotion_item')->find($data['promotion_id']);
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 批量设置订单促销状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setPromotionStatus(array $data)
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['promotion_id', 'in', $data['promotion_id']];
        self::update(['status' => $data['status']], $map);

        return true;
    }

    /**
     * 批量删除订单促销
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delPromotionList(array $data)
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        // 搜索条件
        $map[] = ['promotion_id', 'in', $data['promotion_id']];

        $this->where($map)->delete();
        PromotionItem::where($map)->delete();

        return true;
    }

    /**
     * 获取订单促销列表
     * @access public
     * @param array $data 外部数据
     * @return false|array
     * @throws
     */
    public function getPromotionList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['name']) ?: $map[] = ['name', 'like', '%' . $data['name'] . '%'];
        is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];
        empty($data['begin_time']) ?: $map['begin_time'] = ['< time', $data['end_time']];
        empty($data['end_time']) ?: $map['end_time'] = ['> time', $data['begin_time']];

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['promotion_id' => 'desc'])
            ->with('promotion_item')
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        return $result;
    }

    /**
     * 获取正在进行的促销列表
     * @access public
     * @return false|array
     * @throws
     */
    public function getPromotionActive()
    {
        // 同一个时段内只允许存在一个促销,所以返回get就可以了
        $map[] = ['begin_time', '<=', time()];
        $map[] = ['end_time', '>=', time()];
        $map[] = ['status', '=', 1];

        $with = ['promotion_item' => function ($query) {
            $query->order(['quota' => 'desc']);
        }];

        $result = $this->with($with)->withoutField('status')->where($map)->find();
        return is_null($result) ? [] : $result->toArray();
    }
}
