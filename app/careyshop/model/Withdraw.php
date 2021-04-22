<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    提现模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/26
 */

namespace app\careyshop\model;

use think\facade\Config;
use think\facade\Event;

class Withdraw extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'withdraw_id';

    /**
     * 是否需要自动写入时间戳
     * @var bool|string
     */
    protected $autoWriteTimestamp = true;

    /**
     * 隐藏属性
     * @var string[]
     */
    protected $hidden = [
        'withdraw_id',
    ];

    /**
     * 只读属性
     * @var string[]
     */
    protected $readonly = [
        'withdraw_id',
        'withdraw_no',
        'create_time',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'withdraw_id' => 'integer',
        'user_id'     => 'integer',
        'money'       => 'float',
        'fee'         => 'float',
        'amount'      => 'float',
        'status'      => 'integer',
    ];

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
     * 生成唯一提现单号
     * @access private
     * @return string
     */
    private function getWithdrawNo(): string
    {
        do {
            $withdrawNo = get_order_no('TX_');
        } while (self::checkUnique(['withdraw_no' => $withdrawNo]));

        return $withdrawNo;
    }

    /**
     * 添加交易记录
     * @access public
     * @param int    $type       收入或支出
     * @param float  $amount     总金额
     * @param int    $userId     账号编号
     * @param string $withdrawNo 提现单号
     * @param string $remark     备注
     * @return bool
     */
    private function addTransaction(int $type, float $amount, int $userId, string $withdrawNo, string $remark): bool
    {
        $transactionData = [
            'user_id'    => $userId,
            'type'       => $type,
            'amount'     => $amount,
            'balance'    => UserMoney::where('user_id', '=', $userId)->value('balance'),
            'source_no'  => $withdrawNo,
            'remark'     => $remark,
            'module'     => 'money',
            'to_payment' => Payment::PAYMENT_CODE_USER,
        ];

        $transactionDb = new Transaction();
        if (false === $transactionDb->addTransactionItem($transactionData)) {
            return $this->setError($transactionDb->getError());
        }

        return true;
    }

    /**
     * 获取一个提现请求
     * @access public
     * @param array $data 外部数据
     * @return array|false|mixed
     */
    public function getWithdrawItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $with = [];
        $map[] = ['withdraw.withdraw_no', '=', $data['withdraw_no']];

        if (is_client_admin()) {
            $with['getUser'] = ['username', 'level_icon', 'head_pic', 'nickname'];
        } else {
            $map[] = ['withdraw.user_id', '=', get_client_id()];
        }

        $result[] = $this->alias('withdraw')
            ->withJoin($with)
            ->where($map)
            ->findOrEmpty()
            ->toArray();

        self::keyToSnake(['getUser'], $result);
        return $result[0];
    }

    /**
     * 获取提现请求列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getWithdrawList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['withdraw_no']) ?: $map[] = ['withdraw.withdraw_no', '=', $data['withdraw_no']];
        is_empty_parm($data['status']) ?: $map[] = ['withdraw.status', '=', $data['status']];

        if (!empty($data['begin_time']) && !empty($data['end_time'])) {
            $map[] = ['withdraw.create_time', 'between time', [$data['begin_time'], $data['end_time']]];
        }

        // 关联查询
        $with = [];

        // 后台管理搜索
        if (is_client_admin()) {
            $with['getUser'] = ['username', 'level_icon', 'head_pic', 'nickname'];
            empty($data['account']) ?: $map[] = ['getUser.username', '=', $data['account']];
        } else {
            $map[] = ['withdraw.user_id', '=', get_client_id()];
        }

        $result['total_result'] = $this->alias('withdraw')->withJoin($with)->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setAliasOrder('withdraw')
            ->setDefaultOrder(['withdraw_id' => 'desc'])
            ->alias('withdraw')
            ->withJoin($with)
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        self::keyToSnake(['getUser'], $result['items']);
        return $result;
    }

    /**
     * 申请一个提现请求
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function addWithdrawItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        $map[] = ['withdraw_user_id', '=', $data['withdraw_user_id']];
        $map[] = ['user_id', '=', get_client_id()];

        $userResult = WithdrawUser::where($map)->find();
        if (!$userResult) {
            return $this->setError('提现账号异常');
        }

        // 处理数据
        unset($data['withdraw_id'], $data['remark']);
        $data['fee'] = Config::get('careyshop.system_shopping.withdraw_fee', 0);
        $data['withdraw_no'] = $this->getWithdrawNo();
        $data['user_id'] = get_client_id();
        $data['amount'] = round($data['money'] + (($data['fee'] / 100) * $data['money']), 2);
        $data['status'] = 0;
        $data['name'] = $userResult['name'];
        $data['mobile'] = $userResult['mobile'];
        $data['bank_name'] = $userResult['bank_name'];
        $data['account'] = $userResult['account'];

        // 开启事务
        $this->startTrans();

        try {
            // 添加主表
            $this->save($data);

            // 减少可用余额,并增加锁定余额
            $userMoneyDb = new UserMoney();
            if (!$userMoneyDb->decBalanceAndIncLock($data['amount'], $data['user_id'])) {
                throw new \Exception($userMoneyDb->getError());
            }

            // 添加交易记录
            if (!$this->addTransaction(Transaction::TRANSACTION_EXPENDITURE, $data['amount'], get_client_id(), $this->getAttr('withdraw_no'), '申请提现')) {
                throw new \Exception($this->getError());
            }

            $this->commit();
            $result = $this->hidden(['withdraw_user_id'])->toArray();
            Event::trigger('ApplyWithdraw', $result);

            return $result;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 取消一个提现请求
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function cancelWithdrawItem(array $data): bool
    {
        if (!$this->validateData($data, 'cancel')) {
            return false;
        }

        // 获取主数据
        $map[] = ['withdraw_no', '=', $data['withdraw_no']];
        $map[] = ['user_id', '=', get_client_id()];

        $result = $this->where($map)->find();
        if (is_null($result)) {
            return $this->setError('数据不存在');
        }

        if ($result->getAttr('status') !== 0) {
            return $this->setError('提现状态已不可取消');
        }

        // 开启事务
        $this->startTrans();

        try {
            // 修改主表
            $result->setAttr('status', 2);
            $result->save();

            // 增加可用余额,并减少锁定余额
            $userMoneyDb = new UserMoney();
            $amount = $result->getAttr('amount');

            if (!$userMoneyDb->incBalanceAndDecLock($amount, get_client_id())) {
                throw new \Exception($userMoneyDb->getError());
            }

            // 添加交易记录
            if (!$this->addTransaction(Transaction::TRANSACTION_INCOME, $amount, get_client_id(), $result->getAttr('withdraw_no'), '取消提现')) {
                throw new \Exception($this->getError());
            }

            $this->commit();
            Event::trigger('CancelWithdraw', $result->toArray());

            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 处理一个提现请求
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function processWithdrawItem(array $data): bool
    {
        if (!$this->validateData($data, 'process')) {
            return false;
        }

        $result = $this->where('withdraw_no', '=', $data['withdraw_no'])->find();
        if (is_null($result)) {
            return $this->setError('数据不存在');
        }

        if ($result->getAttr('status') !== 0) {
            return $this->setError('提现状态已不可处理');
        }

        $result->setAttr('status', 1);
        if (false !== $result->save()) {
            Event::trigger('ProcessWithdraw', $result->toArray());
            return true;
        }

        return false;
    }

    /**
     * 完成一个提现请求
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function completeWithdrawItem(array $data): bool
    {
        if (!$this->validateData($data, 'complete')) {
            return false;
        }

        $result = $this->where('withdraw_no', '=', $data['withdraw_no'])->find();
        if (is_null($result)) {
            return $this->setError('数据不存在');
        }

        if ($result->getAttr('status') !== 1) {
            return $this->setError('提现状态不可完成');
        }

        // 开启事务
        $this->startTrans();

        try {
            // 修改主表
            $result->save(['status' => 3, 'remark' => $data['remark']]);

            // 减少锁定余额
            $userMoneyDb = new UserMoney();
            if (!$userMoneyDb->decLockBalance($result->getAttr('amount'), $result->getAttr('user_id'))) {
                throw new \Exception($userMoneyDb->getError());
            }

            $this->commit();
            Event::trigger('CompleteWithdraw', $result->toArray());

            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 拒绝一个提现请求
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function refuseWithdrawItem(array $data): bool
    {
        if (!$this->validateData($data, 'refuse')) {
            return false;
        }

        $result = $this->where('withdraw_no', '=', $data['withdraw_no'])->find();
        if (is_null($result)) {
            return $this->setError('数据不存在');
        }

        if ($result->getAttr('status') !== 0 && $result->getAttr('status') !== 1) {
            return $this->setError('提现状态不可拒绝');
        }

        // 开启事务
        $this->startTrans();

        try {
            // 修改主表
            $result->save(['status' => 4, 'remark' => $data['remark']]);

            // 增加可用余额,并减少锁定余额
            $userMoneyDb = new UserMoney();
            $amount = $result->getAttr('amount');

            if (!$userMoneyDb->incBalanceAndDecLock($amount, $result->getAttr('user_id'))) {
                throw new \Exception($userMoneyDb->getError());
            }

            // 添加交易记录
            if (!$this->addTransaction(Transaction::TRANSACTION_INCOME, $amount, $result->getAttr('user_id'), $result->getAttr('withdraw_no'), '拒绝提现')) {
                throw new \Exception($this->getError());
            }

            $this->commit();
            Event::trigger('RefuseWithdraw', $result->toArray());

            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }
}
