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
     * 发送通知
     * @access public
     * @param array $params 事件外部数据
     */
    public function send(array $params)
    {
        // 解析外部数据成变量
        [
            'data'     => $this->data,      // 订阅者提供数据
            'code'     => $this->code,      // 事件编码(Base)
            'user'     => $this->user,      // 事件对应账号数据
            'variable' => $this->variable,  // 宏替换变量
            'notice'   => $this->notice,    // 通知数据结构
        ] = $params;

        // 根据事件编码获取待发送实际数据
        $this->getReadyData();
    }
}
