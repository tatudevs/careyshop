<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    票据管理模型
 *
 * @author      zxm <252404501@qq.com>
 * @version     v1.1
 * @date        2021/4/11
 */

namespace app\careyshop\model;

use think\facade\Event;

class Invoice extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'invoice_id';

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
     * 隐藏属性
     * @var string[]
     */
    protected $hidden = [
        'user_id',
        'user_invoice_id',
    ];

    /**
     * 只读属性
     * @var string[]
     */
    protected $readonly = [
        'invoice_id',
        'order_no',
        'user_id',
        'user_invoice_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'invoice_id'      => 'integer',
        'user_id'         => 'integer',
        'user_invoice_id' => 'integer',
        'premium'         => 'float',
        'order_amount'    => 'float',
        'invoice_amount'  => 'float',
        'status'          => 'integer',
    ];

    /**
     * belongsTo cs_user_invoice
     * @access public
     * @return object
     */
    public function getInvoice(): object
    {
        return $this
            ->belongsTo(UserInvoice::class, 'user_invoice_id')
            ->joinType('left');
    }

    /**
     * 关联查询NULL处理
     * @param null $value
     * @return object
     */
    public function getGetInvoiceAttr($value = null)
    {
        return $value ?? new \stdClass;
    }

    /**
     * 添加一条票据
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addInvoiceItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 处理部分数据
        $data['user_id'] = is_client_admin() ? $data['client_id'] : get_client_id();
        unset($data['invoice_id'], $data['status'], $data['client_id']);

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一条票据
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setInvoiceItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        $map[] = ['invoice_id', '=', $data['invoice_id']];
        $db = $this->where($map)->find();

        if (is_null($db)) {
            return $this->setError('数据不存在');
        }

        $field = ['number', 'remark', 'status'];
        $isSubscribe = isset($data['status']) && $data['status'] != $db->getAttr('status');

        if (!$db->allowField($field)->save($data)) {
            return false;
        }

        $result = $db->toArray();
        if ($isSubscribe) {
            switch ($result['status']) {
                case 1:
                    Event::trigger('CompleteInvoice', $result);
                    break;

                case 2:
                    Event::trigger('RefuseInvoice', $result);
                    break;
            }
        }

        return $result;
    }

    /**
     * 根据订单号获取一条票据
     * @access public
     * @param array $data 外部数据
     * @return array|false|mixed
     */
    public function getInvoiceOrder(array $data)
    {
        if (!$this->validateData($data, 'order')) {
            return false;
        }

        // 搜索条件
        $map[] = ['invoice.order_no', '=', $data['order_no']];
        is_client_admin() ?: $map[] = ['invoice.user_id', '=', get_client_id()];

        $result[] = $this->withJoin('getInvoice')
            ->where($map)
            ->findOrEmpty()
            ->toArray();

        self::keyToSnake(['getInvoice'], $result);
        return $result[0];
    }

    /**
     * 获取票据列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getInvoiceList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['order_no']) ?: $map[] = ['invoice.order_no', '=', $data['order_no']];
        is_client_admin() ?: $map[] = ['invoice.user_id', '=', get_client_id()];
        is_empty_parm($data['status']) ?: $map[] = ['invoice.status', '=', $data['status']];

        if (!empty($data['begin_time']) && !empty($data['end_time'])) {
            $map[] = ['invoice.create_time', 'between time', [$data['begin_time'], $data['end_time']]];
        }

        $result['total_result'] = $this->alias('invoice')->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setAliasOrder('invoice')
            ->setDefaultOrder(['invoice_id' => 'desc'])
            ->withJoin('getInvoice')
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        self::keyToSnake(['getInvoice'], $result['items']);
        return $result;
    }
}
