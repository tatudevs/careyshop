<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    货到付款
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/22
 */

namespace payment\cod;

use payment\Payment;
use util\Http;

class Cod extends Payment
{
    /**
     * 请求来源
     * @var mixed
     */
    private $request;

    /**
     * 设置请求来源
     * @access public
     * @param string $request 请求
     * @return object
     */
    public function setQequest(string $request): object
    {
        $this->request = $request;
        return $this;
    }

    /**
     * 设置支付配置
     * @access public
     * @return bool
     */
    public function setConfig(): bool
    {
        return true;
    }

    /**
     * 返回支付模块请求结果
     * @access public
     * @return array|bool
     */
    public function payRequest()
    {
        // 请求完成货到付款结算
        $result = Http::httpPost($this->returnUrl, ['out_trade_no' => $this->outTradeNo]);
        return 'web' == $this->request ? ['callback_return_type' => 'view', 'is_callback' => $result] : true;
    }
}
