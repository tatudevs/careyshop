<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    事件订阅基类
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/4/21
 */

namespace app\careyshop\event\subscribe;

class Base
{
    /**
     * 问答-提问被回复
     */
    const EVENT_REPLY_ASK = 2;

    /**
     * 开票-票据已开
     */
    const EVENT_COMPLETE_INVOICE = 4;

    /**
     * 开票-拒绝开票
     */
    const EVENT_REFUSE_INVOICE = 5;

    /**
     * 购物卡-金额增加
     */
    const EVENT_INC_MONEY = 7;

    /**
     * 购物卡-金额减少
     */
    const EVENT_DEC_MONEY = 8;

    /**
     * 商品-咨询被客服回复
     */
    const EVENT_SERVICE_REPLY_CONSULT = 10;

    /**
     * 商品-评价被顾客咨询
     */
    const EVENT_CUSTOMER_ADVISORY_COMMENT = 11;

    /**
     * 商品-评价被客服回复
     */
    const EVENT_SERVICE_REPLY_COMMENT = 12;

    /**
     * 提现-申请提现
     */
    const EVENT_APPLY_WITHDRAW = 14;

    /**
     * 提现-取消提现
     */
    const EVENT_CANCEL_WITHDRAW = 15;

    /**
     * 提现-处理提现
     */
    const EVENT_PROCESS_WITHDRAW = 16;

    /**
     * 提现-完成提现
     */
    const EVENT_COMPLETE_WITHDRAW = 17;

    /**
     * 提现-拒绝提现
     */
    const EVENT_REFUSE_WITHDRAW = 18;

    /**
     * 顾客-账号登录
     */
    const EVENT_USER_LOGIN = 20;

    /**
     * 顾客-账号注册
     */
    const EVENT_USER_REGISTER = 21;

    /**
     * 顾客-修改密码
     */
    const EVENT_CHANGE_PASSWORD = 22;

    /**
     * 顾客-余额增加
     */
    const EVENT_INC_BALANCE = 23;

    /**
     * 顾客-余额减少
     */
    const EVENT_DEC_BALANCE = 24;

    /**
     * 订单-订单创建
     */
    const EVENT_CREATE_ORDER = 26;

    /**
     * 订单-订单取消
     */
    const EVENT_CANCEL_ORDER = 27;

    /**
     * 订单-订单付款
     */
    const EVENT_PAY_ORDER = 28;

    /**
     * 订单-订单配货
     */
    const EVENT_PICKING_ORDER = 29;

    /**
     * 订单-订单发货
     */
    const EVENT_DELIVERY_ORDER = 30;

    /**
     * 订单-订单完成
     */
    const EVENT_COMPLETE_ORDER = 31;

    /**
     * 订单-调整应付金额
     */
    const EVENT_CHANGE_PRICE_ORDER = 32;

    /**
     * 售后-同意售后
     */
    const EVENT_AGREE_SERVICE = 34;

    /**
     * 售后-拒绝售后
     */
    const EVENT_REFUSE_SERVICE = 35;

    /**
     * 售后-正在售后
     */
    const EVENT_AFTER_SERVICE = 36;

    /**
     * 售后-售后撤销
     */
    const EVENT_CANCEL_SERVICE = 37;

    /**
     * 售后-售后完成
     */
    const EVENT_COMPLETE_SERVICE = 38;

    /**
     * 售后-留言被回复
     */
    const EVENT_REPLY_SERVICE = 39;

    /**
     * 售后-商品要求寄回
     */
    const EVENT_SENDBACK_SERVICE = 40;
}
