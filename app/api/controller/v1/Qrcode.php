<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    二维码控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/7/27
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;
use app\careyshop\service\Qrcode as QrcodeService;

class Qrcode extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return void
     */
    protected static function initMethod()
    {
        self::$route = [
            // 动态生成一个二维码
            'get.qrcode.item'    => ['getQrcodeItem'],
            // 添加一个二维码
            'add.qrcode.item'    => ['addQrcodeItem'],
            // 编辑一个二维码
            'set.qrcode.item'    => ['setQrcodeItem'],
            // 获取一个二维码
            'get.qrcode.config'  => ['getQrcodeConfig'],
            // 获取二维码列表
            'get.qrcode.list'    => ['getQrcodeList'],
            // 批量删除二维码
            'del.qrcode.list'    => ['delQrcodeList'],
            // 获取二维码调用地址
            'get.qrcode.callurl' => ['getQrcodeCallurl', QrcodeService::class],
        ];
    }
}
