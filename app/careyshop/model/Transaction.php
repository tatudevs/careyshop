<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    交易结算模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/13
 */

namespace app\careyshop\model;

class Transaction extends CareyShop
{
    /**
     * 收入
     * @var int
     */
    const TRANSACTION_INCOME = 0;

    /**
     * 支出
     * @var int
     */
    const TRANSACTION_EXPENDITURE = 1;

    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'transaction_id';

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
        'transaction_id',
        'create_time',
    ];

    /**
     * 字段类型或者格式转换
     * @var mixed|string[]
     */
    protected $type = [
        'transaction_id' => 'integer',
        'user_id'        => 'integer',
        'type'           => 'integer',
        'amount'         => 'float',
        'balance'        => 'float',
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
     * 关联查询NULL处理
     * @param Object $value
     * @return mixed
     */
    public function getGetUserAttr($value = null)
    {
        return $value ?? new \stdClass;
    }

    /**
     * 添加一条交易结算
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addTransactionItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        if (!isset($data['user_id'])) {
            return $this->setError('交易结算对应账号编号必须填写');
        }

        // 避免无关字段及处理部分数据
        unset($data['transaction_id']);
        $data['action'] = get_client_name();

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 获取一笔交易结算
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getTransactionItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $map[] = ['transaction_id', '=', $data['transaction_id']];
        is_client_admin() ?: $map[] = ['user_id', '=', get_client_id()];

        return $this->where($map)->findOrEmpty()->toArray();
    }

    /**
     * 获取交易结算列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getTransactionList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        is_empty_parm($data['type']) ?: $map[] = ['transaction.type', '=', $data['type']];
        empty($data['source_no']) ?: $map[] = ['transaction.source_no', '=', $data['source_no']];
        is_empty_parm($data['module']) ?: $map[] = ['transaction.module', '=', $data['module']];
        empty($data['card_number']) ?: $map[] = ['transaction.card_number', '=', $data['card_number']];

        if (!empty($data['begin_time']) && !empty($data['end_time'])) {
            $map[] = ['transaction.create_time', 'between time', [$data['begin_time'], $data['end_time']]];
        }

        // 关联查询
        $with = [];

        // 后台管理搜索
        if (is_client_admin()) {
            $with['getUser'] = ['username', 'level_icon', 'head_pic', 'nickname'];
            empty($data['action']) ?: $map[] = ['transaction.action', '=', $data['action']];
            is_empty_parm($data['to_payment']) ?: $map[] = ['transaction.to_payment', '=', $data['to_payment']];
            empty($data['account']) ?: $map[] = ['getUser.username', '=', $data['account']];
        } else {
            $map[] = ['transaction.user_id', '=', get_client_id()];
        }

        // 获取总数量,为空直接返回
        $result['total_result'] = $this->withJoin($with)->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['transaction_id' => 'desc'])
            ->withJoin($with)
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        self::keyToSnake(['getUser'], $result['items']);
        return $result;
    }
}
