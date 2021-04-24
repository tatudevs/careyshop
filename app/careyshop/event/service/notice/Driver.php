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
     * Driver constructor.
     * @access public
     * @param array $params 参数
     */
    public function __construct(array $params)
    {
        // 解析外部数据成变量
        [
            'data'     => $this->data,      // 订阅者提供数据
            'code'     => $this->code,      // 事件编码(Base)
            'user'     => $this->user,      // 事件对应账号数据
            'variable' => $this->variable,  // 宏替换变量
            'notice'   => $this->notice,    // 通知数据结构
        ] = $params;

        // 对订阅者提供的数据进行补齐
        $this->getPaddedData();
    }

    /**
     * 发送通知
     * @access protected
     * @return void
     */
    abstract protected function send();

    /**
     * 对订阅者提供的数据进行补齐
     * @access protected
     * @return void
     */
    protected function getPaddedData()
    {
        // 处理通用数据
        ['username' => $username, 'nickname' => $nickname] = $this->user;
        $this->data['username'] = $username;
        $this->data['nickname'] = empty($nickname) ? $username : $nickname;

        // 不同的事件中存在相同的变量名,因此需要拆分每个事件进行独立获取
        switch ($this->code) {
            case Base::EVENT_INC_MONEY:
            case Base::EVENT_DEC_MONEY:
                $this->data['number'] = auto_hid_substr($this->data['number']);
                break;

            case Base::EVENT_USER_LOGIN:
                $this->data['last_login'] = $this->user['last_login'];
                $this->data['last_ip'] = $this->user['last_ip'] . '(' . $this->user['last_ip_region'] . ')';
                break;

            case Base::EVENT_CREATE_ORDER:
            case Base::EVENT_CANCEL_ORDER:
            case Base::EVENT_PICKING_ORDER:
            case Base::EVENT_DELIVERY_ORDER:
            case Base::EVENT_COMPLETE_ORDER:
                $this->data['goods_name'] = $this->getOrderGoods('order_no');
                break;

            case Base::EVENT_AGREE_SERVICE:
            case Base::EVENT_REFUSE_SERVICE:
            case Base::EVENT_AFTER_SERVICE:
            case Base::EVENT_CANCEL_SERVICE:
            case Base::EVENT_COMPLETE_SERVICE:
            case Base::EVENT_REPLY_SERVICE:
            case Base::EVENT_SENDBACK_SERVICE:
                $type = [0 => '仅退款', 1 => '退货退款', 2 => '换货', 3 => '维修'];
                $this->data['type'] = $type[$this->data['type']];
                $this->data['goods_name'] = $this->getOrderGoods('order_goods_id');
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
