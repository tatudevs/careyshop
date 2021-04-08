<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    优惠劵发放模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/28
 */

namespace app\careyshop\model;

class CouponGive extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'coupon_give_id';

    /**
     * 是否需要自动写入时间戳
     * @var bool|string
     */
    protected $autoWriteTimestamp = true;

    /**
     * 更新日期字段
     * @var false|string
     */
    protected $updateTime = false;

    /**
     * 只读属性
     * @var mixed|string[]
     */
    protected $readonly = [
        'coupon_give_id',
        'coupon_id',
        'exchange_code',
        'create_time',
    ];

    /**
     * 字段类型或者格式转换
     * @var mixed|string[]
     */
    protected $type = [
        'coupon_give_id' => 'integer',
        'coupon_id'      => 'integer',
        'user_id'        => 'integer',
        'order_id'       => 'integer',
        'use_time'       => 'timestamp',
        'is_delete'      => 'integer',
    ];

    /**
     * belongsTo cs_coupon
     * @access public
     * @return object
     */
    public function getCoupon(): object
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    /**
     * hasOne cs_user
     * @access public
     * @return object
     */
    public function getUser(): object
    {
        return $this
            ->hasOne(User::class, 'user_id', 'user_id')
            ->joinType('left');
    }

    /**
     * 关联查询NULL处理
     * @param null $value
     * @return object
     */
    public function getGetUserAttr($value = null)
    {
        return $value ?? new \stdClass;
    }

    /**
     * 使用优惠劵
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function useCouponItem(array $data): bool
    {
        if (!$this->validateData($data, 'use')) {
            return false;
        }

        if (empty($data['coupon_give_id']) && empty($data['exchange_code'])) {
            return $this->setError('优惠劵发放编号或兑换码必须填选其中一个');
        }

        if (!empty($data['coupon_give_id']) && !empty($data['exchange_code'])) {
            return $this->setError('优惠劵发放编号或兑换码只能填选其中一个');
        }

        if (!empty($data['exchange_code'])) {
            $data['user_id'] = get_client_id();
            $map[] = ['exchange_code', '=', $data['exchange_code']];
        } else {
            $map[] = ['user_id', '=', get_client_id()];
            $map[] = ['coupon_give_id', '=', $data['coupon_give_id']];
        }

        $data['use_time'] = time();
        $field = ['user_id', 'order_id', 'order_no', 'use_time'];

        self::update($data, $map, $field);
        $mapCoupon[] = ['coupon_id', '=', $this->where($map)->value('coupon_id', 0)];
        Coupon::where($mapCoupon)->inc('use_num')->update();

        return true;
    }

    /**
     * 发放优惠劵
     * @access public
     * @param int   $couponId 优惠劵编号
     * @param array $userId   发放用户(等同于发放数)
     * @param int   $type     优惠劵类型
     * @return false|int|object
     * @throws
     */
    private function addCouponGive(int $couponId, array $userId, int $type)
    {
        // 获取优惠劵信息
        $map[] = ['coupon_id', '=', $couponId];
        $map[] = ['status', '=', 1];
        $map[] = ['is_invalid', '=', 0];
        $map[] = ['is_delete', '=', 0];

        $couponResult = Coupon::where($map)->find();
        if (is_null($couponResult)) {
            return $this->setError('优惠劵已失效');
        }

        if ($couponResult->getAttr('type') !== $type) {
            return $this->setError('优惠劵发放类型不对应');
        }

        if (2 === $type) {
            $frequency = $couponResult->getAttr('frequency');
            if ($frequency !== 0) {
                $mapUser[] = ['coupon_id', '=', $couponId];
                $mapUser[] = ['user_id', 'in', $userId];

                if ($this->where($mapUser)->count() >= $frequency) {
                    return $this->setError('每人最多只能领取 ' . $frequency . ' 张');
                }
            }

            if (time() < $couponResult->getData('give_begin_time')) {
                return $this->setError('优惠劵领取日期未到');
            }

            if (time() > $couponResult->getData('give_end_time')) {
                return $this->setError('优惠劵领取日期已结束');
            }
        }

        if ($couponResult->getAttr('receive_num') >= $couponResult->getAttr('give_num')) {
            return $this->setError('优惠劵已被领完');
        }

        $remaining = $couponResult->getAttr('give_num') - $couponResult->getAttr('receive_num');
        if (count($userId) > $remaining && $couponResult->getAttr('give_num') !== 0) {
            return $this->setError('可发放数不足，实际需要' . count($userId) . '张');
        }

        // 准备生成的数据
        $data = [];
        foreach ($userId as $value) {
            $data[] = [
                'coupon_id'     => $couponId,
                'user_id'       => $value,
                'exchange_code' => get_randstr(10),
                'create_time'   => time(),
            ];
        }

        // 开启事务
        $this->startTrans();

        try {
            // 写入发放记录
            $result = $type == 3 ? $this->saveAll($data) : $this->insertAll($data);

            // 更新主记录
            $couponResult->where('coupon_id', '=', $couponId)->inc('receive_num', count($userId))->update();

            $this->commit();
            return $result;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 向指定用户发放优惠劵
     * @access public
     * @param array $data 外部数据
     * @return false|int|object
     */
    public function giveCouponUser(array $data)
    {
        if (!$this->validateData($data, 'user')) {
            return false;
        }

        if (empty($data['username']) && empty($data['user_level_id'])) {
            return $this->setError('账号或会员等级必须填选其中一个');
        }

        if (!empty($data['username']) && !empty($data['user_level_id'])) {
            return $this->setError('账号或会员等级只能填选其中一个');
        }

        // 获取账号资料
        $map = [];
        empty($data['username']) ?: $map[] = ['username', 'in', $data['username']];
        empty($data['user_level_id']) ?: $map[] = ['user_level_id', 'in', $data['user_level_id']];

        $userIdResult = User::where($map)->column('user_id');
        if (!$userIdResult) {
            if (!empty($data['username'])) {
                return $this->setError('账号数据不存在');
            }

            if (!empty($data['user_level_id'])) {
                return $this->setError('当前选择的会员等级下不存在可发放账号');
            }

            return $this->setError('未知的错误');
        }

        $result = $this->addCouponGive($data['coupon_id'], $userIdResult, 0);
        if (false !== $result) {
            return $result;
        }

        return false;
    }

    /**
     * 生成线下优惠劵
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function giveCouponLive(array $data): bool
    {
        if (!$this->validateData($data, 'live')) {
            return false;
        }

        if ($this->addCouponGive($data['coupon_id'], array_fill(0, $data['give_number'], 0), 1)) {
            return true;
        }

        return false;
    }

    /**
     * 领取码领取优惠劵
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function giveCouponCode(array $data): bool
    {
        if (!$this->validateData($data, 'code')) {
            return false;
        }

        $result = Coupon::where('give_code', '=', $data['give_code'])->find();
        if (is_null($result)) {
            return $this->setError('优惠劵领取码无效');
        }

        if ($this->addCouponGive($result->getAttr('coupon_id'), [get_client_id()], 2)) {
            return true;
        }

        return false;
    }

    /**
     * 下单送优惠劵(非对外接口)
     * @access public
     * @param int $couponId 优惠劵编号
     * @param int $userId   发放账号Id
     * @return array|false
     */
    public function giveCouponOrder(int $couponId, int $userId)
    {
        $result = $this->addCouponGive($couponId, [$userId], 3);
        if ($result) {
            return $result->column('coupon_give_id');
        }

        return false;
    }

    /**
     * 获取已领取优惠劵列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getCouponGiveList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = $mapOr = [];

        if (is_client_admin()) {
            empty($data['coupon_id']) ?: $map[] = ['coupon_give.coupon_id', '=', $data['coupon_id']];
            empty($data['account']) ?: $map[] = ['getUser.username', '=', $data['account']];
        } else {
            $map[] = ['coupon_give.user_id', '=', get_client_id()];
        }

        if (!is_empty_parm($data['type'])) {
            // 正常状态优惠劵
            if ($data['type'] == 'normal') {
                $map[] = ['coupon_give.use_time', '=', 0];
                $map[] = ['getCoupon.use_end_time', '>', time()];
                $map[] = ['getCoupon.is_invalid', '=', 0];
                $map[] = ['coupon_give.is_delete', '=', 0];
            }

            // 已使用优惠劵
            if ($data['type'] == 'used') {
                $map[] = ['coupon_give.use_time', '<>', 0];
                $map[] = ['coupon_give.is_delete', '=', 0];
            }

            // 无效优惠劵
            if ($data['type'] == 'invalid') {
                $map[] = ['coupon_give.use_time', '=', 0];
                $mapOr[] = ['getCoupon.use_end_time', '<', time()];
                $mapOr[] = ['getCoupon.is_invalid', '=', 1];
                $map[] = ['coupon_give.is_delete', '=', 0];
            }

            // 回收站优惠劵
            if ($data['type'] == 'delete') {
                $map[] = ['coupon_give.is_delete', '=', 1];
            }
        } else {
            $map[] = ['coupon_give.is_delete', '=', 0];
        }

        // 关联查询
        $with = ['getCoupon'];
        !is_client_admin() ?: $with['getUser'] = ['username', 'nickname', 'level_icon', 'head_pic'];

        $result['total_result'] = $this
            ->withJoin($with)
            ->where($map)
            ->where(function ($query) use ($mapOr) {
                $query->whereOr($mapOr);
            })
            ->count();

        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setAliasOrder('coupon_give')
            ->setDefaultOrder(['coupon_give_id' => 'desc'])
            ->withoutField('is_delete')
            ->withJoin($with)
            ->where($map)
            ->where(function ($query) use ($mapOr) {
                $query->whereOr($mapOr);
            })
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->hidden(['getCoupon.is_delete'])
            ->toArray();

        self::keyToSnake(['getCoupon', 'getUser'], $result['items']);
        return $result;
    }

    /**
     * 批量删除已领取优惠劵
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delCouponGiveList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        // 搜索条件
        $map[] = ['coupon_give_id', 'in', $data['coupon_give_id']];

        if (is_client_admin()) {
            // 未使用的进行物理删除
            $this->where($map)->where('use_time', '=', 0)->delete();
            // 已使用的放入回收站
            $this->where($map)->where('use_time', '<>', 0)->save(['is_delete' => 1]);
        } else {
            $this->where($map)->where('user_id', '=', get_client_id())->save(['is_delete' => 1]);
        }

        return true;
    }

    /**
     * 批量恢复已删优惠劵
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function recCouponGiveList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        $map[] = ['coupon_give_id', 'in', $data['coupon_give_id']];
        is_client_admin() ?: $map[] = ['user_id', '=', get_client_id()];
        self::update(['is_delete' => 0], $map);

        return true;
    }

    /**
     * 导出线下生成的优惠劵
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getCouponGiveExport(array $data)
    {
        if (!$this->validateData($data, 'export')) {
            return false;
        }

        return $this
            ->withoutField('coupon_id,user_id,order_id')
            ->where('coupon_id', '=', $data['coupon_id'])
            ->select()
            ->toArray();
    }

    /**
     * 根据商品Id列出可使用的优惠劵
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getCouponGiveSelect(array $data)
    {
        if (!$this->validateData($data, 'select')) {
            return false;
        }

        // 获取未使用的优惠劵
        $map[] = ['user_id', '=', get_client_id()];
        $map[] = ['use_time', '=', 0];
        $map[] = ['is_delete', '=', 0];

        $couponResult = $this
            ->with(['get_coupon' => function ($query) {
                $query->order('money', 'desc');
            }])
            ->where($map)
            ->select()
            ->hidden(['is_delete', 'get_coupon.is_delete'])
            ->toArray();

        if (empty($couponResult)) {
            return [];
        }

        // 获取订单商品分类并进行筛选
        $result = [];
        $goodsResult = Goods::where('goods_id', 'in', $data['goods_id'])->column('goods_category_id');

        // 优惠劵发放服务层实例化
        $giveSer = new \app\careyshop\service\CouponGive();
        foreach ($couponResult as $value) {
            $temp = $value;
            $temp['is_use'] = (int)$giveSer->checkCoupon($temp, $goodsResult, $data['pay_amount']);
            $temp['not_use_error'] = 0 == $temp['is_use'] ? $giveSer->getError() : '';

            $result[] = $temp;
            unset($temp);
        }

        return $result;
    }

    /**
     * 验证优惠劵是否可使用
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getCouponGiveCheck(array $data)
    {
        if (!$this->validateData($data, 'check')) {
            return false;
        }

        if (empty($data['coupon_give_id']) && empty($data['exchange_code'])) {
            return $this->setError('优惠劵发放编号或兑换码必须填选其中一个');
        }

        if (!empty($data['coupon_give_id']) && !empty($data['exchange_code'])) {
            return $this->setError('优惠劵发放编号或兑换码只能填选其中一个');
        }

        // 获取优惠劵数据
        $map[] = ['use_time', '=', 0];
        $map[] = ['is_delete', '=', 0];

        if (!empty($data['exchange_code'])) {
            $map[] = ['exchange_code', '=', $data['exchange_code']];
        } else {
            $map[] = ['user_id', '=', get_client_id()];
            $map[] = ['coupon_give_id', '=', $data['coupon_give_id']];
        }

        // 获取未使用的优惠劵
        $couponResult = $this->with('get_coupon')->where($map)->find();
        if (is_null($couponResult)) {
            return $this->setError('优惠劵不存在');
        }

        // 获取订单商品分类并进行筛选
        $result = $couponResult->hidden(['is_delete', 'get_coupon.is_delete'])->toArray();
        $goodsResult = Goods::where('goods_id', 'in', $data['goods_id'])->column('goods_category_id');

        // 优惠劵发放服务层实例化
        $giveSer = new \app\careyshop\service\CouponGive();
        if (!$giveSer->checkCoupon($result, $goodsResult, $data['pay_amount'])) {
            return $this->setError($giveSer->getError());
        }

        return $result;
    }
}
