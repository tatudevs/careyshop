<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    账号管理事件订阅
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/4/20
 */

namespace app\careyshop\event\subscribe;

use think\Event;

class User
{
    /**
     * 账号登录事件触发
     * @access public
     * @param array $userData 账号数据
     */
    public function onUserLogin(array $userData)
    {
    }

    /**
     * 账号注册事件触发
     * @access public
     * @param array $userData 账号数据
     */
    public function onUserRegister(array $userData)
    {
    }

    /**
     * 修改密码事件触发
     * @access public
     * @param array $userData 账号数据
     */
    public function onChangePassword(array $userData)
    {
    }

    /**
     * 余额增加事件触发
     * @access public
     * @param array $moneyData 资金变动数据
     */
    public function onIncBalance(array $moneyData)
    {
    }

    /**
     * 余额减少事件触发
     * @access public
     * @param array $moneyData 资金变动数据
     */
    public function onDecBalance(array $moneyData)
    {
    }

    /**
     * 事件订阅
     * @access public
     * @param Event $event 事件对象
     */
    public function subscribe(Event $event)
    {
        // 账号登录
        $event->listen('UserLogin', [$this, 'onUserLogin']);
        // 账号注册
        $event->listen('UserRegister', [$this, 'onUserRegister']);
        // 修改密码
        $event->listen('ChangePassword', [$this, 'onChangePassword']);
        // 余额增加
        $event->listen('IncBalance', [$this, 'onIncBalance']);
        // 余额减少
        $event->listen('DecBalance', [$this, 'onDecBalance']);
    }
}
