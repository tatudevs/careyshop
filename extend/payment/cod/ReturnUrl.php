<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    货到付款同步返回
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/22
 */

namespace payment\cod;

class ReturnUrl
{
    /**
     * 流水号
     * @var mixed
     */
    public $paymentNo;

    /**
     * 返回流水号
     * @access public
     * @return string
     */
    public function getPaymentNo(): string
    {
        return $this->paymentNo;
    }

    /**
     * 返回总金额
     * @access public
     * @return int
     */
    public function getTotalAmount(): int
    {
        return 0;
    }

    /**
     * 返回交易号
     * @access public
     * @return string
     */
    public function getTradeNo(): string
    {
        return rand_number(28);
    }

    /**
     * 返回交易时间
     * @access public
     * @return string
     */
    public function getTimestamp(): string
    {
        return date('Y-m-d H:i:s', time());
    }

    /**
     * 返回支付成功页面
     * @access public
     * @param  string $msg 消息内容
     * @return array
     */
    public function getSuccess($msg = '支付结算完成'): array
    {
        $data['callback_return_type'] = 'view';
        $data['is_callback'] = sprintf(
            '<head><meta http-equiv="refresh" content="0; url=%s?info=%s&payment_no=%s"><title></title></head>',
            config('careyshop.payment.success'),
            $msg,
            $this->paymentNo
        );

        return $data;
    }

    /**
     * 返回支付失败页面
     * @access public
     * @param  string $msg 消息内容
     * @return array
     */
    public function getError($msg = '支付结算失败'): array
    {
        $data['callback_return_type'] = 'view';
        $data['is_callback'] = sprintf(
            '<head><meta http-equiv="refresh" content="0; url=%s?info=%s&payment_no=%s"><title></title></head>',
            config('careyshop.payment.error'),
            $msg,
            $this->paymentNo
        );

        return $data;
    }

    /**
     * 验签方法
     * @access public
     * @return bool
     */
    public function checkReturn(): bool
    {
        $this->paymentNo = input('param.out_trade_no');
        return true;
    }
}
