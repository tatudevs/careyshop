<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    交易结算控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/6/20
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;

class Transaction extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return void
     */
    protected static function initMethod()
    {
        self::$route = [
            // 获取一笔交易结算
            'get.transaction.item' => ['getTransactionItem'],
            // 获取交易结算列表
            'get.transaction.list' => ['getTransactionList'],
        ];
    }
}
