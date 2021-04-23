<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    通知模板驱动
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/4/20
 */

namespace app\careyshop\event\service\notice;

use app\careyshop\event\subscribe\Base;
use app\careyshop\model\OrderGoods;
use think\helper\Str;

abstract class Driver
{
    /**
     * @var array 订阅者提供数据
     */
    protected array $data = [];

    /**
     * @var int 事件编码(Base)
     */
    protected int $code;

    /**
     * @var array 事件对应账号数据
     */
    protected array $user = [];

    /**
     * @var array 宏替换变量
     */
    protected array $variable = [];

    /**
     * @var array 通知数据结构
     */
    protected array $notice = [];

    /**
     * @var array 待发送实际数据
     */
    protected array $ready = [];

    /**
     * 发送通知
     * @access protected
     * @param array $params 参数
     * @return void
     */
    abstract protected function send(array $params);

    /**
     * 根据事件编码获取待发送实际数据
     * @access protected
     * @return void
     */
    protected function getReadyData()
    {
        // 处理通用数据
        $this->ready = [];
        ['username' => $username, 'nickname' => $nickname] = $this->user;
        $this->ready['username'] = $username;
        $this->ready['nickname'] = empty($nickname) ? $username : $nickname;

        // 不同的事件中存在相同的变量名,因此需要拆分每个事件进行独立获取
        switch ($this->code) {
            case Base::EVENT_COMPLETE_INVOICE:
            case Base::EVENT_REFUSE_INVOICE:
                $this->ready['order_no'] = $this->data['order_no'];
                $this->ready['number'] = $this->data['number'];
                break;

            case Base::EVENT_INC_MONEY:
            case Base::EVENT_DEC_MONEY:
                $this->ready['initial'] = $this->data['initial'];
                $this->ready['money'] = $this->data['money'];
                $this->ready['balance'] = $this->data['balance'];
                $this->ready['number'] = auto_hid_substr($this->data['number']);
                break;

            case Base::EVENT_APPLY_WITHDRAW:
            case Base::EVENT_CANCEL_WITHDRAW:
            case Base::EVENT_PROCESS_WITHDRAW:
            case Base::EVENT_COMPLETE_WITHDRAW:
            case Base::EVENT_REFUSE_WITHDRAW:
                $this->ready['withdraw_no'] = $this->data['withdraw_no'];
                $this->ready['money'] = $this->data['money'];
                break;

            case Base::EVENT_USER_LOGIN:
                $this->ready['last_login'] = $this->user['last_login'];
                $this->ready['last_ip'] = $this->user['last_ip'] . '(' . $this->user['last_ip_region'] . ')';
                break;

            case Base::EVENT_USER_REGISTER:
                $this->ready['password'] = $this->data['password'];
                break;

            case Base::EVENT_INC_BALANCE:
            case Base::EVENT_DEC_BALANCE:
                $this->ready['initial'] = $this->data['initial'];
                $this->ready['money'] = $this->data['money'];
                $this->ready['balance'] = $this->data['balance'];
                break;

            case Base::EVENT_CREATE_ORDER:
            case Base::EVENT_CANCEL_ORDER:
            case Base::EVENT_PICKING_ORDER:
            case Base::EVENT_DELIVERY_ORDER:
            case Base::EVENT_COMPLETE_ORDER:
                $this->ready['order_no'] = $this->data['order_no'];
                $this->ready['create_time'] = $this->data['create_time'];
                $this->ready['update_time'] = $this->data['update_time'];
                $this->ready['picking_time'] = $this->data['picking_time'];
                $this->ready['delivery_time'] = $this->data['delivery_time'];
                $this->ready['finished_time'] = $this->data['finished_time'];
                $this->ready['total_amount'] = $this->data['total_amount'];
                $this->ready['pay_amount'] = $this->data['pay_amount'];
                $this->ready['complete'] = $this->data['complete_address'];
                $this->ready['delivery_name'] = $this->data['delivery_name'] ?? '';
                $this->ready['logistic_code'] = $this->data['logistic_code'] ?? '';
                $this->ready['goods_name'] = $this->getOrderGoods('order_no');
                break;

            case Base::EVENT_PAY_ORDER:
                $this->ready['order_no'] = $this->data['order_no'];
                $this->ready['amount'] = $this->data['amount'];
                $this->ready['payment_time'] = $this->data['payment_time'];
                break;

            case Base::EVENT_CHANGE_PRICE_ORDER:
                $this->ready['order_no'] = $this->data['order_no'];
                $this->ready['total_amount'] = $this->data['total_amount'];
                break;

            case Base::EVENT_AGREE_SERVICE:
            case Base::EVENT_REFUSE_SERVICE:
            case Base::EVENT_AFTER_SERVICE:
            case Base::EVENT_CANCEL_SERVICE:
            case Base::EVENT_COMPLETE_SERVICE:
            case Base::EVENT_REPLY_SERVICE:
            case Base::EVENT_SENDBACK_SERVICE:
                $type = [0 => '仅退款', 1 => '退货退款', 2 => '换货', 3 => '维修'];
                $this->ready['order_goods_id'] = $this->data['order_goods_id'];
                $this->ready['order_no'] = $this->data['order_no'];
                $this->ready['service_no'] = $this->data['service_no'];
                $this->ready['reason'] = $this->data['reason'];
                $this->ready['qty'] = $this->data['qty'];
                $this->ready['result'] = $this->data['result'];
                $this->ready['create_time'] = $this->data['create_time'];
                $this->ready['update_time'] = $this->data['update_time'];
                $this->ready['type'] = $type[$this->data['type']];
                $this->ready['goods_name'] = $this->getOrderGoods('order_goods_id');
                break;
        }
    }

    /**
     * 获取订单商品名称
     * @access private
     * @param string $field 查询字段名
     * @return string
     * @throws
     */
    private function getOrderGoods(string $field): string
    {
        $result = OrderGoods::where($field, '=', $this->data[$field])
            ->select()
            ->toArray();

        if (empty($result)) {
            return '';
        }

        $name = Str::substr($result[0]['goods_name'], 0, 24) . '...';
        if (count($result) > 1) {
            $total = array_sum(array_column($result, 'qty'));
            $name .= "（合计：${total}件）";
        }

        return $name;
    }
}
