<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    提现服务层
 *
 * @author      zxm <252404501@qq.com>
 * @version     v1.1
 * @date        2018/1/26
 */

namespace app\common\service;

use think\Config;

class Withdraw extends CareyShop
{
    /**
     * 获取提现手续费
     * @access public
     * @return array
     */
    public function getWithdrawFee()
    {
        return ['withdraw_fee' => Config::get('withdraw_fee.value', 'system_info')];
    }
}