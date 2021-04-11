<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    票据管理控制器
 *
 * @author      zxm <252404501@qq.com>
 * @version     v1.1
 * @date        2021/4/11
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;

class Invoice extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return void
     */
    protected static function initMethod()
    {
        self::$route = [
            // 添加一条票据
            'add.invoice.item'  => ['addInvoiceItem'],
            // 编辑一条票据
            'set.invoice.item'  => ['setInvoiceItem'],
            // 根据订单号获取一条票据
            'get.invoice.order' => ['getInvoiceOrder'],
            // 获取票据列表
            'get.invoice.list'  => ['getInvoiceList'],
        ];
    }
}
