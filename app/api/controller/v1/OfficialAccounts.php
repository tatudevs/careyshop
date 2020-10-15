<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    公众号控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/13
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;
use app\common\service\OfficialAccounts as OfficialService;

class OfficialAccounts extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return void
     */
    protected static function initMethod()
    {
        self::$route = [
            // 获取框架已支持的公众号平台
            'get.official.platform' => ['getOfficialPlatform', OfficialService::class],
            // 根据公众号所属模块获取默认配置结构
            'get.official.setting'  => ['getOfficialStting'],
            // 添加一个公众号
            'add.official.item'     => ['addOfficialItem'],
            // 编辑一个公众号
            'set.official.item'     => ['setOfficialItem'],
            // 获取一个公众号
            'get.official.item'     => ['getOfficialItem'],
            // 获取公众号列表
            'get.official.list'     => ['getOfficialList'],
            // 批量删除公众号
            'del.official.list'     => ['delOfficialList'],
            // 批量设置公众号状态
            'set.official.status'   => ['setOfficialStatus'],
        ];
    }
}
