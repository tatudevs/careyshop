<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    通知系统服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/3
 */

namespace app\common\service;

class Notice extends CareyShop
{
    /**
     * 获取通知系统列表
     * @access public
     * @return array|false
     */
    public static function getNoticeList()
    {
        $result = config('careyshop.notice');
        foreach ($result as $key => $value) {
            if (!empty($value)) {
                $result[$key] = json_decode($value, true);
            }
        }

        return $result;
    }
}
