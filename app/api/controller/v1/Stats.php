<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    数据统计控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/9/17
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;

class Stats extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return array
     */
    protected static function initMethod()
    {
        return [
            // 获取后台首页统计数据
            'get.stats.index' => ['getStatsIndex'],
        ];
    }
}
