<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    购物卡事件订阅
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/4/20
 */

namespace app\careyshop\event\subscribe;

use app\careyshop\event\service\notice\Notice;
use think\Event;

class Card extends Base
{
    /**
     * 金额增加
     * @access public
     * @param array $card 资金数据
     */
    public function onIncMoney(array $card)
    {
        Notice::instance()->send($card, self::EVENT_INC_MONEY);
    }

    /**
     * 金额减少
     * @access public
     * @param array $card 资金数据
     */
    public function onDecMoney(array $card)
    {
        Notice::instance()->send($card, self::EVENT_DEC_MONEY);
    }

    /**
     * 事件订阅
     * @access public
     * @param Event $event 事件对象
     */
    public function subscribe(Event $event)
    {
        // 金额增加
        $event->listen('IncMoney', [$this, 'onIncMoney']);
        // 金额减少
        $event->listen('DecMoney', [$this, 'onDecMoney']);
    }
}
