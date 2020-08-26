<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    交易日志模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/21
 */

namespace app\common\model;

class PaymentLog extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'payment_log_id';

    /**
     * 是否需要自动写入时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'payment_log_id',
    ];

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'payment_log_id',
        'payment_no',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'payment_log_id' => 'integer',
        'user_id'        => 'integer',
        'amount'         => 'float',
        'type'           => 'integer',
        'status'         => 'integer',
        'to_payment'     => 'integer',
    ];

    /**
     * hasOne cs_user
     * @access public
     * @return mixed
     */
    public function getUser()
    {
        return $this
            ->hasOne(User::class, 'user_id', 'user_id')
            ->joinType('left');
    }

    /**
     * 生成唯一交易流水号
     * @access private
     * @return string
     */
    private function getPaymentNo()
    {
        do {
            $paymentNo = get_order_no('ZF_');
        } while (self::checkUnique(['payment_no' => ['eq', $paymentNo]]));

        return $paymentNo;
    }

    /**
     * 添加一笔交易日志
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addPaymentLogItem($data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 初始化部分数据
        unset($data['payment_log_id'], $data['payment_time'], $data['to_payment']);
        $this->setAttr('payment_log_id', null);
        $data['payment_no'] = $this->getPaymentNo();
        $data['user_id'] = get_client_id();
        isset($data['status']) ?: $data['status'] = 0;

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 关闭一笔充值记录
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function closePaymentLogItem($data)
    {
        if (!$this->validateData($data, 'close')) {
            return false;
        }

        $map[] = ['payment_no', '=', $data['payment_no']];
        $map[] = ['user_id', '=', get_client_id()];

        $result = $this->where($map)->find();
        if (is_null($result)) {
            return $this->setError('数据不存在');
        }

        if ($result->getAttr('status') !== 0) {
            return $this->setError('状态不可变更');
        }

        $result->setAttr('status', 2);
        $result->save();

        return true;
    }

    /**
     * 获取一笔充值记录
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getPaymentLogItem($data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $map[] = ['payment_no', '=', $data['payment_no']];
        is_client_admin() ?: $map[] = ['user_id', '=', get_client_id()];
        !isset($data['type']) ?: $map[] = ['type', '=', $data['type']];
        !isset($data['status']) ?: $map[] = ['status', '=', $data['status']];

        $result = $this->where($map)->find();
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 获取充值记录列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getPaymentLogList($data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['payment_no']) ?: $map[] = ['payment_log.payment_no', '=', $data['payment_no']];
        empty($data['order_no']) ?: $map[] = ['payment_log.order_no', '=', $data['order_no']];
        empty($data['out_trade_no']) ?: $map[] = ['payment_log.out_trade_no', '=', $data['out_trade_no']];
        is_empty_parm($data['type']) ?: $map[] = ['payment_log.type', '=', $data['type']];
        is_empty_parm($data['status']) ?: $map[] = ['payment_log.status', '=', $data['status']];

        if (!empty($data['begin_time']) && !empty($data['end_time'])) {
            $map[] = ['payment_log.create_time', 'between time', [$data['begin_time'], $data['end_time']]];
        }

        // 关联查询
        $with = [];
        if (is_client_admin()) {
            $with['getUser'] = ['username', 'nickname', 'level_icon', 'head_pic'];
            is_empty_parm($data['to_payment']) ?: $map[] = ['payment_log.to_payment', '=', (string)$data['to_payment']];
            empty($data['account']) ?: $map[] = ['getUser.username|getUser.nickname', '=', $data['account']];
        } else {
            $map[] = ['payment_log.user_id', '=', get_client_id()];
        }

        // 获取总数量,为空直接返回
        $result['total_result'] = $this->withJoin($with)->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        $result['items'] = $this->setDefaultOrder(['payment_log_id' => 'desc'])
            ->withJoin($with)
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        self::keyToSnake(['getUser'], $result['items']);
        return $result;
    }

    /**
     * 获取一笔订单成功付款的具体金额
     * @access public
     * @param string $paymentNo 交易流水号
     * @return float|int
     */
    public static function getPaymentLogValue($paymentNo)
    {
        if (empty($paymentNo)) {
            return 0;
        }

        $map[] = ['payment_no', '=', $paymentNo];
        $map[] = ['type', '=', 1];
        $map[] = ['status', '=', 1];

        return self::where($map)->value('amount', 0);
    }
}
