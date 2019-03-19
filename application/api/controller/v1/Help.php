<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    帮助文档控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2019/3/19
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;

class Help extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return array
     */
    protected static function initMethod()
    {
        return [
            // 添加一条帮助文档
            'add.help.item' => ['addHelpItem'],
            // 编辑一条帮助文档
            'set.help.item' => ['setHelpItem'],
            // 获取一条帮助文档
            'get.help.item' => ['getHelpItem'],
            // ...待续
        ];
    }
}
