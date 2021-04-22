<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    支付配置模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/31
 */

namespace app\careyshop\model;

use think\facade\Cache;
use app\careyshop\validate\Recharge as Validate;
use think\facade\Event;

class Payment extends CareyShop
{
    /**
     * 账号资金
     * @var int
     */
    const PAYMENT_CODE_USER = 0;

    /**
     * 货到付款
     * @var int
     */
    const PAYMENT_CODE_COD = 1;

    /**
     * 支付宝
     * @var int
     */
    const PAYMENT_CODE_ALIPAY = 2;

    /**
     * 微信支付
     * @var int
     */
    const PAYMENT_CODE_WECHAT = 3;

    /**
     * 银行转账
     * @var int
     */
    const PAYMENT_CODE_BANK = 4;

    /**
     * 购物卡
     * @var int
     */
    const PAYMENT_CODE_CARD = 5;

    /**
     * 其他
     * @var int
     */
    const PAYMENT_CODE_OTHER = 6;

    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'payment_id';

    /**
     * 只读属性
     * @var string[]
     */
    protected $readonly = [
        'payment_id',
        'name',
        'code',
        'model',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'payment_id' => 'integer',
        'code'       => 'integer',
        'is_deposit' => 'integer',
        'is_inpour'  => 'integer',
        'is_payment' => 'integer',
        'is_refund'  => 'integer',
        'setting'    => 'array',
        'sort'       => 'integer',
        'status'     => 'integer',
    ];

    /**
     * 添加一个支付配置
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addPaymentItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段及数据初始化
        unset($data['payment_id']);
        !empty($data['setting']) ?: $data['setting'] = [];

        if ($this->save($data)) {
            Cache::tag('Payment')->clear();
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个支付配置
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setPaymentItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        if (isset($data['setting']) && '' == $data['setting']) {
            $data['setting'] = [];
        }

        $map[] = ['payment_id', '=', $data['payment_id']];
        $result = self::update($data, $map);
        Cache::tag('Payment')->clear();

        return $result->toArray();
    }

    /**
     * 批量删除支付配置
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delPaymentList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        self::destroy($data['payment_id']);
        Cache::tag('Payment')->clear();

        return true;
    }

    /**
     * 获取一个支付配置
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getPaymentItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        return $this->cache(true, null, 'Payment')->findOrEmpty($data['payment_id'])->toArray();
    }

    /**
     * 根据Code获取支付配置详情(不对外开放)
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getPaymentInfo(array $data)
    {
        if (!$this->validateData($data, 'info')) {
            return false;
        }

        $map[] = ['code', '=', $data['code']];
        $map[] = ['status', '=', $data['status']];

        return $this->cache(true, null, 'Payment')
            ->withoutField('image,sort,status')
            ->where($map)
            ->findOrEmpty()
            ->toArray();
    }

    /**
     * 获取支付配置列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getPaymentList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 查询条件
        $map = [];
        is_client_admin() ?: $map[] = ['status', '=', 1];
        empty($data['exclude_code']) ?: $map[] = ['code', 'not in', $data['exclude_code']];

        if (!is_empty_parm($data['type'])) {
            switch ($data['type']) {
                case 'deposit':
                    $map[] = ['is_deposit', '=', 1];
                    break;
                case 'inpour':
                    $map[] = ['is_inpour', '=', 1];
                    break;
                case 'payment':
                    $map[] = ['is_payment', '=', 1];
                    break;
                case 'refund':
                    $map[] = ['is_refund', '=', 1];
                    break;
            }
        }

        // 返回字段
        $field = 'payment_id,name,code,image,is_deposit,is_inpour,is_payment,is_refund,setting,model,sort,status';
        !empty($data['is_select']) && $field = 'name,code,image';

        // 实际查询
        return $this->setDefaultOrder(['payment_id' => 'asc'], ['sort' => 'asc'])
            ->cache(true, null, 'Payment')
            ->field($field)
            ->where($map)
            ->withSearch(['order'])
            ->select()
            ->toArray();
    }

    /**
     * 设置支付配置排序
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setPaymentSort(array $data): bool
    {
        if (!$this->validateData($data, 'sort')) {
            return false;
        }

        $map[] = ['payment_id', '=', $data['payment_id']];
        self::update(['sort' => $data['sort']], $map);
        Cache::tag('Payment')->clear();

        return true;
    }

    /**
     * 根据编号自动排序
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setPaymentIndex(array $data): bool
    {
        if (!$this->validateData($data, 'index')) {
            return false;
        }

        foreach ($data['payment_id'] as $key => $value) {
            self::update(['sort' => $key + 1], ['payment_id' => $value]);
        }

        Cache::tag('Payment')->clear();
        return true;
    }

    /**
     * 批量设置支付配置状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setPaymentStatus(array $data): bool
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['payment_id', 'in', $data['payment_id']];
        self::update(['status' => $data['status']], $map);
        Cache::tag('Payment')->clear();

        return true;
    }

    /**
     * 财务对账号进行资金调整
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setPaymentFinance(array $data): bool
    {
        if (!$this->validateData($data, 'finance', false, Validate::class)) {
            return false;
        }

        if (!isset($data['money']) && !isset($data['points'])) {
            return $this->setError('资金或积分调整数量必须填写');
        }

        $paymentResult = $this->getPaymentInfo(['code' => $data['to_payment'], 'status' => 1]);
        if (!$paymentResult || $paymentResult['is_deposit'] != 1) {
            return $this->setError('支付方式不可用');
        }

        $userMap[] = ['user_id', '=', $data['client_id']];
        $userMap[] = ['status', '=', 1];

        if (!User::checkUnique($userMap)) {
            return $this->setError('账号不存在或已被禁用');
        }

        // 开启事务
        $this->startTrans();

        try {
            $userMoneyDb = new UserMoney();
            $transactionDb = new Transaction();

            // 调整可用余额
            if (!empty($data['money'])) {
                if (!$userMoneyDb->setBalance($data['money'], $data['client_id'])) {
                    throw new \Exception($userMoneyDb->getError());
                }

                $txMoneyData = [
                    'user_id'    => $data['client_id'],
                    'type'       => $data['money'] > 0 ? 0 : 1,
                    'amount'     => sprintf('%.2f', $data['money'] > 0 ? $data['money'] : -$data['money']),
                    'balance'    => $userMoneyDb->where('user_id', '=', $data['client_id'])->value('balance', 0),
                    'source_no'  => !empty($data['source_no']) ? $data['source_no'] : get_order_no('TZ_'),
                    'remark'     => '财务调整',
                    'cause'      => $data['cause'],
                    'module'     => 'money',
                    'to_payment' => $data['to_payment'],
                ];

                if (!$transactionDb->addTransactionItem($txMoneyData)) {
                    throw new \Exception($transactionDb->getError());
                }
            }

            if (!empty($data['points'])) {
                // 调整账号积分
                if (!$userMoneyDb->setPoints($data['points'], $data['client_id'])) {
                    throw new \Exception($userMoneyDb->getError());
                }

                $txPointsData = [
                    'user_id'    => $data['client_id'],
                    'type'       => $data['points'] > 0 ? 0 : 1,
                    'amount'     => $data['points'] > 0 ? $data['points'] : -$data['points'],
                    'balance'    => $userMoneyDb->where('user_id', '=', $data['client_id'])->value('points', 0),
                    'source_no'  => !empty($data['source_no']) ? $data['source_no'] : get_order_no('TZ_'),
                    'remark'     => '财务调整',
                    'cause'      => $data['cause'],
                    'module'     => 'points',
                    'to_payment' => $data['to_payment'],
                ];

                if (!$transactionDb->addTransactionItem($txPointsData)) {
                    throw new \Exception($transactionDb->getError());
                }
            }

            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 账号在线充值余额
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function userPaymentPay(array $data)
    {
        if (!$this->validateData($data, 'user', false, Validate::class)) {
            return false;
        }

        // 获取支付配置信息
        $paymentResult = $this->getPaymentInfo(['code' => $data['to_payment'], 'status' => 1]);
        if (!$paymentResult || $paymentResult['is_inpour'] != 1) {
            return $this->setError('支付方式不可用');
        }

        // 当支付流水号存在,则恢复支付
        $paymentLogResult = null;
        $paymentLogDb = new PaymentLog();

        // 获取已存在未支付的支付日志
        $logData['type'] = 0;
        if (!empty($data['payment_no'])) {
            $logData['payment_no'] = $data['payment_no'];
            $logData['status'] = 0;
            $paymentLogResult = $paymentLogDb->getPaymentLogItem($logData);

            if (!$paymentLogResult) {
                return $this->setError($paymentLogDb->getError());
            }

            // 支付金额不匹配则返回错误
            if (bccomp($paymentLogResult['amount'], $data['money'], 2) !== 0) {
                return $this->setError('支付金额发生变化，请重新创建订单');
            }
        }

        // 创建新的支付日志
        if (!$paymentLogResult) {
            $logData['amount'] = $data['money'];
            $paymentLogResult = $paymentLogDb->addPaymentLogItem($logData);

            if (!$paymentLogResult) {
                return $this->setError($paymentLogDb->getError());
            }
        }

        $paymentSer = new \app\careyshop\service\Payment();
        $result = $paymentSer->createPaymentPay($paymentLogResult, $paymentResult, $data['request_type'], '账号充值');

        if (false === $result) {
            return $this->setError($paymentSer->getError());
        }

        return $result;
    }

    /**
     * 订单付款在线支付
     * @access public
     * @param array $data 外部数据
     * @return array|bool
     * @throws
     */
    public function orderPaymentPay(array $data)
    {
        if (!$this->validateData($data, 'order', false, Validate::class)) {
            return false;
        }

        // 获取订单信息
        $map[] = ['order_no', '=', $data['order_no']];
        $map[] = ['user_id', '=', get_client_id()];
        $map[] = ['is_delete', '=', 0];

        $orderDb = new Order();
        $result = $orderDb->where($map)->find();

        if (is_null($result)) {
            return $this->setError('订单不存在');
        }

        if ($result->getAttr('trade_status') !== 0) {
            return $this->setError('订单不可支付');
        }

        if ($result->getAttr('payment_status') === 1) {
            return $this->setError('订单已完成支付');
        }

        // 创建新的支付日志
        $logData = [
            'order_no' => $result->getAttr('order_no'),
            'amount'   => $data['to_payment'] != self::PAYMENT_CODE_COD ? $result->getAttr('total_amount') : 0,
            'type'     => 1,
            'status'   => 0,
        ];

        $paymentLogDb = new PaymentLog();
        $paymentLogResult = $paymentLogDb->addPaymentLogItem($logData);

        if (!$paymentLogResult) {
            return $this->setError($paymentLogDb->getError());
        }

        // 应付金额为0时直接内部处理
        $paymentSer = new \app\careyshop\service\Payment();
        if (round($paymentLogResult['amount'], 2) <= 0) {
            $model = $paymentSer->createPaymentModel('cod', 'return_url');
            if (false === $model) {
                return $this->setError($paymentSer->getError());
            }

            $model->paymentNo = $paymentLogDb->getAttr('payment_no');
            return $this->settleOrder($model, $paymentLogDb, $data['to_payment']);
        }

        // 获取支付配置信息
        $paymentResult = $this->getPaymentInfo(['code' => $data['to_payment'], 'status' => 1]);
        if (!$paymentResult || $paymentResult['is_payment'] != 1) {
            return $this->setError('支付方式不可用');
        }

        $createResult = $paymentSer->createPaymentPay($paymentLogResult, $paymentResult, $data['request_type'], '订单付款');
        if (false === $createResult) {
            return $this->setError($paymentSer->getError());
        }

        return $createResult;
    }

    /**
     * 接收支付返回内容
     * @access public
     * @param array $data 外部数据
     * @return false|string
     * @throws
     */
    public function putPaymentData(array $data)
    {
        if (!$this->validateData($data, 'put', false, Validate::class)) {
            return false;
        }

        // 获取支付配置信息
        $paymentResult = $this->getPaymentInfo(['code' => $data['to_payment'], 'status' => 1]);
        if (!$paymentResult) {
            return $this->setError('支付方式不可用');
        }

        // 创建支付总控件
        $model = $data['type'] == 'return' ? 'return_url' : 'notify_url';
        $paymentSer = new \app\careyshop\service\Payment();
        $payment = $paymentSer->createPaymentModel($paymentResult['model'], $model);

        if (false === $payment) {
            return $this->setError($paymentSer->getError());
        }

        // 初始化配置,并且进行验签
        if (!$payment->checkReturn($paymentResult['setting'])) {
            return $payment->getError('非法访问');
        }

        // 获取支付日志信息
        $paymentLogResult = PaymentLog::where('payment_no', '=', $payment->getPaymentNo())->find();
        if (is_null($paymentLogResult)) {
            return $payment->getError('数据不存在');
        }

        // 已完成支付
        if ($paymentLogResult->getAttr('status') !== 0) {
            return $payment->getSuccess();
        }

        // 结算实际业务
        $result = false;
        switch ($paymentLogResult->getAttr('type')) {
            case 0:
                $result = $this->settlePay($payment, $paymentLogResult, $paymentResult['code']);
                break;

            case 1:
                $result = $this->settleOrder($payment, $paymentLogResult, $paymentResult['code']);
                break;
        }

        return false === $result ? $payment->getError() : $payment->getSuccess();
    }

    /**
     * 结算订单付款
     * @access private
     * @param object $model        支付模块
     * @param object $paymentLogDb 支付日志
     * @param int    $toPayment    支付方式
     * @return bool
     */
    private function settleOrder(object $model, object $paymentLogDb, int $toPayment): bool
    {
        // 共用参数提取
        $userId = $paymentLogDb->getAttr('user_id');
        $amount = $model->getTotalAmount();

        // 实付金额不得小于应付金额
        if (bccomp($amount, $paymentLogDb->getAttr('amount'), 2) === -1) {
            return false;
        }

        if (bccomp($amount, 0, 2) === 0 && $toPayment != self::PAYMENT_CODE_COD) {
            $toPayment = self::PAYMENT_CODE_USER;
        }

        // 开启事务
        $this->startTrans();

        try {
            // 保存支付日志
            $paymentLogDb->save([
                'out_trade_no' => $model->getTradeNo(),
                'payment_time' => $model->getTimestamp(),
                'to_payment'   => $toPayment,
                'status'       => 1,
            ]);

            // 调整订单状态
            $orderDb = new Order();
            $orderResult = $orderDb->isPaymentStatus(['order_no' => $paymentLogDb->getAttr('order_no')]);

            if (!$orderResult || $orderResult->getAttr('user_id') != $userId) {
                throw new \Exception();
            }

            // 保存订单数据
            $orderResult->save([
                'payment_no'     => $model->getPaymentNo(),
                'payment_code'   => $toPayment,
                'payment_status' => 1,
                'payment_time'   => strtotime($model->getTimestamp()),
            ]);

            // 保存订单操作日志
            $orderLogData = [
                'order_id'        => $orderResult->getAttr('order_id'),
                'order_no'        => $orderResult->getAttr('order_no'),
                'trade_status'    => $orderResult->getAttr('trade_status'),
                'delivery_status' => $orderResult->getAttr('delivery_status'),
                'payment_status'  => $orderResult->getAttr('payment_status'),
            ];

            if (!$orderDb->addOrderLog($orderLogData, '订单付款成功', '订单付款')) {
                throw new \Exception();
            }

            Event::trigger('PayOrder', $paymentLogDb->toArray());
            $this->commit();

            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 结算账号充值
     * @access private
     * @param object $model        支付模块
     * @param object $paymentLogDb 支付日志
     * @param int    $toPayment    支付方式
     * @return bool
     */
    private function settlePay(object $model, object $paymentLogDb, int $toPayment): bool
    {
        // 共用参数提取
        $userId = $paymentLogDb->getAttr('user_id');
        $amount = $model->getTotalAmount();

        // 开启事务
        $this->startTrans();

        try {
            // 保存支付日志
            $paymentLogDb->save([
                'out_trade_no' => $model->getTradeNo(),
                'amount'       => $amount,
                'payment_time' => $model->getTimestamp(),
                'to_payment'   => $toPayment,
                'status'       => 1,
            ]);

            // 调整账号充值金额
            if (!(new UserMoney())->setBalance($amount, $userId)) {
                throw new \Exception();
            }

            // 保存交易结算日志
            (new Transaction())->addTransactionItem([
                'user_id'    => $userId,
                'type'       => Transaction::TRANSACTION_INCOME,
                'amount'     => $amount,
                'balance'    => UserMoney::where('user_id', '=', $userId)->value('balance', 0),
                'source_no'  => $model->getPaymentNo(),
                'remark'     => '账号充值',
                'module'     => 'money',
                'to_payment' => $toPayment,
            ]);

            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }
}
