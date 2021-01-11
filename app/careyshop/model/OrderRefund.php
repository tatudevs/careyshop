<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    订单退款模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/9/9
 */

namespace app\careyshop\model;

class OrderRefund extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'order_refund_id';

    /**
     * 是否需要自动写入时间戳
     * @var bool|string
     */
    protected $autoWriteTimestamp = true;

    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'order_refund_id',
    ];

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'order_refund_id',
        'refund_no',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'order_refund_id' => 'integer',
        'user_id'         => 'integer',
        'total_amount'    => 'float',
        'amount'          => 'float',
        'to_payment'      => 'integer',
        'status'          => 'integer',
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
     * 关联查询NULL处理
     * @param mixed $value
     * @return mixed|\stdClass
     */
    public function getGetUserAttr($value)
    {
        return is_null($value) ? new \stdClass : $value;
    }

    /**
     * 生成唯一退款流水号
     * @access public
     * @return string
     */
    public function getRefundNo(): string
    {
        do {
            $refundNo = get_order_no('TK_');
        } while (self::checkUnique(['refund_no' => $refundNo]));

        return $refundNo;
    }

    /**
     * 添加一个订单退款记录
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function addOrderRefundItem(array $data): bool
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 初始化部分数据
        unset($data['order_refund_id']);
        isset($data['refund_no']) ?: $data['refund_no'] = $this->getRefundNo();
        isset($data['status']) ?: $data['status'] = 0;

        if ($this->save($data)) {
            return true;
        }

        return false;
    }

    /**
     * 取消订单后支付金额原路退回
     * @access public
     * @param array $orderData 订单结构数据
     * @param float $amount    自定义金额
     * @param null  $refundNo  退款单号
     * @return bool
     */
    public function refundOrderPayment(array $orderData, $amount = 0.0, &$refundNo = null): bool
    {
        if (!$this->validateData($orderData, 'refund')) {
            return false;
        }

        if ($orderData['total_amount'] <= 0 || empty($orderData['payment_no'])) {
            return true;
        }

        // 准备日志数据
        $refundLog = [
            'refund_no'    => $this->getRefundNo(),
            'order_no'     => $orderData['order_no'],
            'user_id'      => $orderData['user_id'],
            'total_amount' => $orderData['total_amount'],
            'amount'       => bccomp($amount, 0, 2) === 0 ? $orderData['total_amount'] : $amount,
            'payment_no'   => $orderData['payment_no'],
            'to_payment'   => $orderData['payment_code'],
        ];

        // 获取支付配置信息
        $paymentDb = new Payment();
        $paymentResult = $paymentDb->getPaymentInfo(['code' => $orderData['payment_code'], 'status' => 1]);

        if (!$paymentResult || $paymentResult['is_refund'] != 1) {
            return $this->setError('支付方式不支持原路退款');
        }

        // 向第三方支付平台申请退款
        $refundSer = new \app\careyshop\service\OrderRefund();
        $result = $refundSer->createRefundRequest($orderData, $paymentResult, $refundLog['amount'], $refundLog['refund_no']);

        if (false === $result || !is_object($result)) {
            return $this->setError($refundSer->getError());
        }

        // 申请成功后写入日志数据
        $refundLog['out_trade_no'] = $result->getTradeNo();
        $refundLog['status'] = 1;
        !isset($refundNo) ?: $refundNo = $refundLog['refund_no'];

        return $this->addOrderRefundItem($refundLog);
    }

    /**
     * 原路退款申请失败后尝试重试
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function retryRefundItem(array $data): bool
    {
        if (!$this->validateData($data, 'retry')) {
            return false;
        }

        $map[] = ['refund_no', '=', $data['refund_no']];
        $map[] = ['status', '=', 2];
        is_client_admin() ?: $map[] = ['user_id', '=', get_client_id()];

        $refundDb = $this->where($map)->find();
        if (is_null($refundDb)) {
            return $this->setError('数据不存在');
        }

        // 获取支付配置信息
        $paymentDb = new Payment();
        $paymentResult = $paymentDb->getPaymentInfo(['code' => $refundDb->getAttr('to_payment'), 'status' => 1]);

        if (!$paymentResult || $paymentResult['is_refund'] != 1) {
            $this->setError('支付方式不支持原路退款');
            $refundDb->save(['out_trade_msg' => $this->getError(), 'status' => 2]);
            return false;
        }

        // 向第三方支付平台申请退款
        $refundData = $refundDb->toArray();
        $refundSer = new \app\careyshop\service\OrderRefund();
        $result = $refundSer->createRefundRequest($refundData, $paymentResult, $refundData['amount'], $refundData['refund_no']);

        if (false === $result || !is_object($result)) {
            $refundDb->save(['out_trade_msg' => $refundSer->getError(), 'status' => 2]);
            return $this->setError($refundSer->getError());
        }

        // 申请成功后写入日志数据
        $refundLog['out_trade_no'] = $result->getTradeNo();
        $refundLog['status'] = 1;

        return false !== $refundDb->save($refundLog);
    }

    /**
     * 查询一笔退款信息
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function queryRefundItem(array $data)
    {
        if (!$this->validateData($data, 'query')) {
            return false;
        }

        $map[] = ['refund_no', '=', $data['refund_no']];
        $map[] = ['status', '=', 1];
        is_client_admin() ?: $map[] = ['user_id', '=', get_client_id()];

        $refundLog = $this->where($map)->find();
        if (is_null($refundLog)) {
            return $this->setError('数据不存在');
        }

        // 获取支付配置信息
        $paymentDb = new Payment();
        $paymentResult = $paymentDb->getPaymentInfo(['code' => $refundLog->getAttr('to_payment'), 'status' => 1]);

        if (!$paymentResult || $paymentResult['is_refund'] != 1) {
            return $this->setError('支付方式不支持原路退款');
        }

        // 查询退款
        $refundSer = new \app\careyshop\service\OrderRefund();
        $result = $refundSer->createFastpayRefundQueryRequest($refundLog->toArray(), $paymentResult);

        if (false === $result) {
            return $this->setError($refundSer->getError());
        }

        return $result;
    }

    /**
     * 获取退款记录列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getRefundList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['refund_no']) ?: $map[] = ['order_refund.refund_no', '=', $data['refund_no']];
        empty($data['order_no']) ?: $map[] = ['order_refund.order_no', '=', $data['order_no']];
        empty($data['out_trade_no']) ?: $map[] = ['order_refund.out_trade_no', '=', $data['out_trade_no']];
        empty($data['payment_no']) ?: $map[] = ['order_refund.payment_no', '=', $data['payment_no']];
        is_empty_parm($data['status']) ?: $map[] = ['order_refund.status', '=', $data['status']];

        if (!empty($data['begin_time']) && !empty($data['end_time'])) {
            $map[] = ['order_refund.create_time', 'between time', [$data['begin_time'], $data['end_time']]];
        }

        if (is_client_admin()) {
            !isset($data['to_payment']) ?: $map[] = ['order_refund.to_payment', '=', $data['to_payment']];
            empty($data['account']) ?: $map[] = ['getUser.username', '=', $data['account']];
        } else {
            $map[] = ['order_refund.user_id', '=', get_client_id()];
        }

        // 关联查询
        $with = is_client_admin() ? ['getUser' => ['username', 'nickname', 'level_icon', 'head_pic']] : [];

        // 获取总数量,为空直接返回
        $result['total_result'] = $this->alias('order_refund')->withJoin($with)->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['order_refund_id' => 'desc'])
            ->alias('order_refund')
            ->withJoin($with)
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        self::keyToSnake(['getUser'], $result['items']);
        return $result;
    }
}
