<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    支付基类
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/22
 */

namespace payment;

class Payment
{
    /**
     * 错误信息
     * @var string
     */
    protected string $error = '';

    /**
     * 同步返回URL
     * @var string
     */
    protected string $returnUrl;

    /**
     * 异步返回URL
     * @var string
     */
    protected string $notifyUrl;

    /**
     * 支付流水号
     * @var string
     */
    protected string $outTradeNo;

    /**
     * 订单名称
     * @var string
     */
    protected string $subject;

    /**
     * 支付金额
     * @var float
     */
    protected float $totalAmount;

    /**
     * 支付描述
     * @var string
     */
    protected string $body = '';

    /**
     * 返回错误信息
     * @access public
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * 设置同步返回URL
     * @access public
     * @param string $returnUrl 同步返回URL
     * @return bool
     */
    public function setReturnUrl(string $returnUrl): bool
    {
        if (is_string($returnUrl)) {
            $this->returnUrl = $returnUrl;
            return true;
        }

        return false;
    }

    /**
     * 设置异步返回URL
     * @access public
     * @param string $notifyUrl 异步返回URL
     * @return bool
     */
    public function setNotifyUrl(string $notifyUrl): bool
    {
        if (is_string($notifyUrl)) {
            $this->notifyUrl = $notifyUrl;
            return true;
        }

        return false;
    }

    /**
     * 设置支付流水号
     * @access public
     * @param string $paymentNo 流水号
     */
    public function setOutTradeNo(string $paymentNo)
    {
        $this->outTradeNo = $paymentNo;
    }

    /**
     * 设置支付订单名称
     * @access public
     * @param string $subject 订单名称
     */
    public function setSubject(string $subject)
    {
        $this->subject = $subject;
    }

    /**
     * 设置订单支付金额
     * @access public
     * @param float $amount 支付金额
     */
    public function setTotalAmount(float $amount)
    {
        $this->totalAmount = $amount;
    }

    /**
     * 设置支付描述
     * @access public
     * @param string $body 描述
     */
    public function setBody($body = '')
    {
        $this->body = $body;
    }
}
