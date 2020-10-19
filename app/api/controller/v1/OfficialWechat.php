<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    微信公众号控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/19
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;
use app\common\wechat\service\OfficialAccount;

class OfficialWechat extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return void
     */
    protected static function initMethod()
    {
        self::$route = [
            // 接收微信推送信息
            'put.official.wechat.data' => ['putOfficialWechatData', OfficialAccount::class],
        ];
    }
}
