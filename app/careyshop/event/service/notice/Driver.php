<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    通知模板驱动
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/4/20
 */

namespace app\careyshop\event\service\notice;

abstract class Driver
{
    /**
     * @var string 驱动名称
     */
    protected string $name;

    /**
     * 发送通知
     * @access protected
     * @param array $params 参数
     * @return void
     */
    abstract protected function send(array $params);
}
