<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    提现服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/3
 */

namespace app\common\service;

class Withdraw extends CareyShop
{
    /**
     * 获取提现手续费
     * @access public
     * @return float[]
     */
    public function getWithdrawFee(): array
    {
        return ['withdraw_fee' => (float)config('careyshop.system_shopping.withdraw_fee')];
    }
}
