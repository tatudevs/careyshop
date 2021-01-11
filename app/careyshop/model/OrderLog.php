<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    订单日志模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/3
 */

namespace app\careyshop\model;

class OrderLog extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'order_log_id';

    /**
     * 是否需要自动写入时间戳
     * @var bool|string
     */
    protected $autoWriteTimestamp = true;

    /**
     * 更新日期字段
     * @var bool|string
     */
    protected $updateTime = false;

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'order_log_id',
        'order_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'order_log_id'    => 'integer',
        'order_id'        => 'integer',
        'trade_status'    => 'integer',
        'delivery_status' => 'integer',
        'payment_status'  => 'integer',
        'client_type'     => 'integer',
    ];

    /**
     * 添加订单操作日志
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function addOrderItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['order_log_id']);
        $data['action'] = get_client_name();
        $data['client_type'] = get_client_type();

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 获取一个订单操作日志
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getOrderLog(array $data)
    {
        if (!$this->validateData($data, 'log')) {
            return false;
        }

        // 判断订单所属
        if (!is_client_admin()) {
            $orderMap[] = ['user_id', '=', get_client_id()];
            $orderMap[] = ['order_no', '=', $data['order_no']];

            if (Order::where($orderMap)->count() <= 0) {
                return [];
            }
        }

        return $this->where('order_no', '=', $data['order_no'])
            ->order(['order_log_id' => 'desc'])
            ->select()
            ->toArray();
    }
}
