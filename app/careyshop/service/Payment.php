<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    支付配置服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/31
 */

namespace app\careyshop\service;

use think\exception\ValidateException;
use think\facade\Route;
use think\helper\Str;

class Payment extends CareyShop
{
    /**
     * 获取支付异步URL接口
     * @access public
     * @param array $data     外部数据
     * @param bool  $isString 是否直接返回URL地址
     * @return string|array|bool
     */
    public function getPaymentNotify(array $data, $isString = false)
    {
        // 规则验证
        try {
            validate(\app\careyshop\validate\Recharge::class)->scene('return')->check($data);
        } catch (ValidateException $e) {
            return $this->setError($e->getMessage());
        }

        // 生成链接
        $vars = ['method' => 'put.payment.data', 'to_payment' => $data['to_payment'], 'type' => 'notify'];
        $notifyUrl = Route::buildUrl("api/{$this->version}/payment", $vars)->domain(true)->build();

        return $isString ? $notifyUrl : ['notify_url' => $notifyUrl, 'to_payment' => $data['to_payment']];
    }

    /**
     * 获取支付同步URL接口
     * @access public
     * @param array $data     外部数据
     * @param bool  $isString 是否直接返回URL地址
     * @return string|array|bool
     */
    public function getPaymentReturn(array $data, $isString = false)
    {
        // 规则验证
        try {
            validate(\app\careyshop\validate\Recharge::class)->scene('return')->check($data);
        } catch (ValidateException $e) {
            return $this->setError($e->getMessage());
        }

        // 生成链接
        $vars = ['method' => 'put.payment.data', 'to_payment' => $data['to_payment'], 'type' => 'return'];
        $notifyUrl = Route::buildUrl("api/{$this->version}/payment", $vars)->domain(true)->build();

        return $isString ? $notifyUrl : ['return_url' => $notifyUrl, 'to_payment' => $data['to_payment']];
    }

    /**
     * 创建支付模块
     * @access public
     * @param string $file  支付目录
     * @param string $model 支付模块
     * @return mixed
     */
    public function createPaymentModel(string $file, string $model)
    {
        // 转换模块的名称
        $file = Str::lower($file);
        $model = Str::studly($model);

        if (empty($file) || empty($model)) {
            return $this->setError('支付目录或模块不存在');
        }

        $payment = '\\payment\\' . $file . '\\' . $model;
        if (class_exists($payment)) {
            return new $payment;
        }

        return $this->setError($payment . '支付模块不存在');
    }

    /**
     * 创建支付请求
     * @access public
     * @param array  &$data    支付日志
     * @param array  &$setting 支付配置
     * @param string  $request 请求来源
     * @param string  $subject 订单名称
     * @param string  $body    订单描述
     * @return array|false
     */
    public function createPaymentPay(array &$data, array &$setting, string $request, string $subject, $body = '')
    {
        if (empty($data) || !is_array($setting)) {
            return $this->setError('数据错误');
        }

        // 创建支付总控件
        $payment = $this->createPaymentModel($setting['model'], $setting['model']);
        if (false === $payment) {
            return false;
        }

        // 设置支付配置
        if (!$payment->setQequest($request)->setConfig($setting['setting'])) {
            return $this->setError($payment->getError());
        }

        // 设置支付同步返回URL
        if (!$payment->setReturnUrl($this->getPaymentReturn(['to_payment' => $setting['code']], true))) {
            return false;
        }

        // 设置支付异步返回URL
        if (!$payment->setNotifyUrl($this->getPaymentNotify(['to_payment' => $setting['code']], true))) {
            return false;
        }

        // 设置支付流水号
        $payment->setOutTradeNo($data['payment_no']);

        // 设置支付订单名称
        $payment->setSubject($subject);

        // 设置支付金额
        $payment->setTotalAmount($data['amount']);

        // 设置支付描述
        $payment->setBody($body);

        // 返回支付模块请求结果
        $result = $payment->payRequest();

        return false === $result ? $this->setError($payment->getError()) : $result;
    }
}
