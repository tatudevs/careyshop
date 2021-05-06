<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    支付宝同步返回
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/23
 */

namespace payment\alipay;

use AopClient;

require_once __DIR__ . '/lib/AopClient.php';

class ReturnUrl
{
    /**
     * 流水号
     * @var string
     */
    protected string $paymentNo;

    /**
     * 总金额
     * @var float
     */
    protected float $totalAmount;

    /**
     * 交易号
     * @var string
     */
    protected string $tradeNo;

    /**
     * 交易时间
     * @var string
     */
    protected string $timestamp;

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
     * @return float
     */
    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    /**
     * 返回交易号
     * @access public
     * @return string
     */
    public function getTradeNo(): string
    {
        return $this->tradeNo;
    }

    /**
     * 返回交易时间
     * @access public
     * @return string
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * 返回支付成功页面
     * @access public
     * @param string $msg 消息内容
     * @return array
     */
    public function getSuccess(string $msg = '支付结算完成'): array
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
     * @param string $msg 消息内容
     * @return array
     */
    public function getError(string $msg = '支付结算失败'): array
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
     * @param null $setting 配置参数
     * @return bool
     */
    public function checkReturn($setting = null): bool
    {
        if (empty($setting)) {
            return false;
        }

        $arr = $_GET;
        $this->paymentNo = $arr['out_trade_no'] ?? 0;
        $this->totalAmount = $arr['total_amount'] ?? 0;
        $this->tradeNo = $arr['trade_no'] ?? '';
        $this->timestamp = $arr['timestamp'] ?? '';

        if (!isset($arr['sign_type'])) {
            return false;
        }

        $aop = new AopClient();
        $aop->alipayrsaPublicKey = $setting['alipayPublicKey']['value'];

        if (!$aop->rsaCheckV1($arr, $aop->alipayrsaPublicKey, $arr['sign_type'])) {
            return false;
        }

        if ($arr['app_id'] != $setting['appId']['value']) {
            return false;
        }

        return true;
    }
}
