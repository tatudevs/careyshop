<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    订单日志模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/8/12
 */

namespace app\common\model;

class OrderLog extends CareyShop
{
    /**
     * 是否需要自动写入时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    /**
     * 更新日期字段
     * @var bool/string
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
     * @param  array $data 外部数据
     * @return false|array
     * @throws
     */
    public function addOrderItem($data)
    {
        if (!$this->validateData($data, 'OrderLog')) {
            return false;
        }

        // 避免无关字段
        unset($data['order_log_id']);
        $data['action'] = get_client_name();
        $data['client_type'] = get_client_type();

        if (false !== $this->isUpdate(false)->allowField(true)->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 获取一个订单操作日志
     * @access public
     * @param $data
     * @return array|bool
     * @throws
     */
    public function getOrderLog($data)
    {
        if (!$this->validateData($data, 'OrderLog.log')) {
            return false;
        }

        // 判断订单所属
        if (!is_client_admin()) {
            $orderMap['user_id'] = ['eq', get_client_id()];
            $orderMap['order_no'] = ['eq', $data['order_no']];

            if (Order::where($orderMap)->count() <= 0) {
                return [];
            }
        }

        $result = self::all(function ($query) use ($data) {
            $query
                ->where(['order_no' => ['eq', $data['order_no']]])
                ->order(['order_log_id' => 'desc']);
        });

        if (false !== $result) {
            return $result->toArray();
        }

        return false;
    }
}
