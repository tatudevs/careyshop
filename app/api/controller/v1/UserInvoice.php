<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    账号发票信息控制器
 *
 * @author      zxm <252404501@qq.com>
 * @version     v1.1
 * @date        2021/4/11
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;

class UserInvoice extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return void
     */
    protected static function initMethod()
    {
        self::$route = [
            // 获取一个发票信息
            'get.user.invoice.item'      => ['getUserInvoiceItem'],
            // 获取发票信息列表
            'get.user.invoice.list'      => ['getUserInvoiceList'],
            // 添加一个发票信息
            'add.user.invoice.item'      => ['addUserInvoiceItem'],
            // 编辑一个发票信息
            'set.user.invoice.item'      => ['setUserInvoiceItem'],
            // 批量删除发票信息
            'del.user.invoice.list'      => ['delUserInvoiceList'],
            // 检测是否超出最大添加数量
            'check.user.invoice.maximum' => ['checkUserInvoiceMaximum'],
        ];
    }
}
