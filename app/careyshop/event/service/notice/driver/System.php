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
    /**
     * @var string 驱动名称
     */
    protected string $name = 'System';

    public function send(array $params)
    {
        // 解析外部数据成变量
        [
            'data'     => $data,        // 事件触发时初始提供的数据
            'code'     => $code,        // 事件编码(Base)
            'user'     => $user,        // 事件触发对应账号数据
            'variable' => $variable,    // 宏替换变量
            'notice'   => $notice,      // 通知系统数据结构
        ] = $params;
    }
}
