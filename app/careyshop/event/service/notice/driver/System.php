<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    系统通知驱动
 *
 * @author      zxm <252404501@qq.com>
 * @version     v1.1
 * @date        2021/4/20
 */

namespace app\careyshop\event\service\notice\driver;

use app\careyshop\event\service\notice\Driver;

class System extends Driver
{
    public function send(array $params)
    {
        dump($params);exit();
    }
}
