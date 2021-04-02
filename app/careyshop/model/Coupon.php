<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    优惠劵模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/28
 */

namespace app\careyshop\model;

use think\facade\Db;

class Coupon extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'coupon_id';

    /**
     * 只读属性
     * @var mixed|string[]
     */
    protected $readonly = [
        'coupon_id',
        'give_code',
    ];

    /**
     * 字段类型或者格式转换
     * @var mixed|string[]
     */
    protected $type = [
        'coupon_id'        => 'integer',
        'type'             => 'integer',
        'money'            => 'float',
        'quota'            => 'float',
        'category'         => 'array',
        'exclude_category' => 'array',
        'level'            => 'array',
        'frequency'        => 'integer',
        'give_num'         => 'integer',
        'receive_num'      => 'integer',
        'use_num'          => 'integer',
        'give_begin_time'  => 'timestamp',
        'give_end_time'    => 'timestamp',
        'use_begin_time'   => 'timestamp',
        'use_end_time'     => 'timestamp',
        'status'           => 'integer',
        'is_invalid'       => 'integer',
        'is_delete'        => 'integer',
    ];

    /**
     * 领取码自动完成
     * @access private
     * @param mixed $type 参数
     * @return string
     */
    private function getGiveCode($type): string
    {
        $value = '';
        if (2 == $type) {
            do {
                $value = get_randstr(10);
            } while (self::checkUnique(['give_code' => $value]));
        }

        return $value;
    }

    /**
     * 添加一张优惠劵
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addCouponItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段并初始化
        unset($data['coupon_id'], $data['receive_num'], $data['use_num']);
        !empty($data['category']) ?: $data['category'] = [];
        !empty($data['exclude_category']) ?: $data['exclude_category'] = [];
        !empty($data['level']) ?: $data['level'] = [];
        $data['give_code'] = $this->getGiveCode($data['type']);

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一张优惠劵
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setCouponItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        // 避免不允许修改字段
        unset($data['type'], $data['give_code'], $data['receive_num'], $data['use_num']);

        // 处理数组字段
        if (isset($data['category']) && '' == $data['category']) {
            $data['category'] = [];
        }

        if (isset($data['exclude_category']) && '' == $data['exclude_category']) {
            $data['exclude_category'] = [];
        }

        if (isset($data['level']) && '' == $data['level']) {
            $data['level'] = [];
        }

        $map[] = ['coupon_id', '=', $data['coupon_id']];
        $map[] = ['is_delete', '=', 0];

        $result = self::update($data, $map);
        return $result->toArray();
    }

    /**
     * 获取一张优惠劵
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getCouponItem(array $data)
    {
        if (!$this->validateData($data, 'get')) {
            return false;
        }

        $map[] = ['coupon_id', '=', $data['coupon_id']];
        $map[] = ['is_delete', '=', 0];

        return $this->withoutField('is_delete')->where($map)->findOrEmpty()->toArray();
    }

    /**
     * 获取优惠劵列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getCouponList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map[] = ['is_delete', '=', 0];
        empty($data['name']) ?: $map[] = ['name', 'like', '%' . $data['name'] . '%'];
        is_empty_parm($data['type']) ?: $map[] = ['type', '=', $data['type']];
        is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];
        is_empty_parm($data['is_invalid']) ?: $map[] = ['is_invalid', '=', $data['is_invalid']];

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['coupon_id' => 'desc'])
            ->withoutField('is_delete')
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        return $result;
    }

    /**
     * 获取优惠劵选择列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getCouponSelect(array $data)
    {
        if (!$this->validateData($data, 'select')) {
            return false;
        }

        // 搜索条件
        $map[] = ['is_delete', '=', 0];
        is_empty_parm($data['type']) ?: $map[] = ['type', '=', $data['type']];
        is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];
        is_empty_parm($data['is_invalid']) ?: $map[] = ['is_invalid', '=', $data['is_invalid']];

        if (!empty($data['is_shelf_life'])) {
            $map[] = ['give_end_time', '> time', time()];
            $map[] = ['use_end_time', '> time', time()];
        }

        return $this
            ->where($map)
            ->order(['coupon_id' => 'desc'])
            ->select()
            ->toArray();
    }

    /**
     * 批量删除优惠劵
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delCouponList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        $map[] = ['coupon_id', 'in', $data['coupon_id']];
        $map[] = ['is_delete', '=', 0];

        self::update(['is_delete' => 1], $map);
        return true;
    }

    /**
     * 批量设置优惠劵状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setCouponStatus(array $data): bool
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['coupon_id', 'in', $data['coupon_id']];
        $map[] = ['is_delete', '=', 0];

        self::update(['status' => $data['status']], $map);
        return true;
    }

    /**
     * 批量设置优惠劵是否失效
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setCouponInvalid(array $data): bool
    {
        if (!$this->validateData($data, 'invalid')) {
            return false;
        }

        $map[] = ['coupon_id', 'in', $data['coupon_id']];
        $map[] = ['is_delete', '=', 0];

        self::update(['is_invalid' => $data['is_invalid']], $map);
        return true;
    }

    /**
     * 获取当前可领取的优惠劵列表
     * @access public
     * @return array
     * @throws
     */
    public function getCouponActive(): array
    {
        // 搜索条件
        $map[] = ['type', '=', 2];
        $map[] = ['give_num', 'exp', Db::raw('> `receive_num`')];
        $map[] = ['give_begin_time', '<= time', time()];
        $map[] = ['give_end_time', '> time', time()];
        $map[] = ['status', '=', 1];
        $map[] = ['is_invalid', '=', 0];
        $map[] = ['is_delete', '=', 0];

        // 过滤字段
        $field = 'coupon_id,type,use_num,status,is_invalid,is_delete';

        // 实际查询
        return $this
            ->withoutField($field)
            ->where($map)
            ->select()
            ->toArray();
    }
}
