<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    数据统计服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/9/17
 */

namespace app\common\service;

class Stats extends CareyShop
{
    /**
     * 获取后台首页统计数据
     * @access public
     * @return array
     */
    public static function getStatsIndex()
    {
        return [];
    }

    /**
     * 获取店铺统计数据
     * @access public
     * @param int|string $begin 起始日期
     * @param int|string $end   截止日期
     * @return array
     */
    public static function getStatsShop($begin, $end)
    {
        return [];
    }

    /**
     * 获取商品统计数据
     * @access public
     * @param int|string $begin 起始日期
     * @param int|string $end   截止日期
     * @return array
     */
    public static function getStatsGoods($begin, $end)
    {
        return [];
    }

    /**
     * 获取订单统计数据
     * @access public
     * @param int|string $begin 起始日期
     * @param int|string $end   截止日期
     * @return array
     */
    public static function getStatsOrder($begin, $end)
    {
        return [];
    }

    /**
     * 获取会员统计数据
     * @access public
     * @param int|string $begin 起始日期
     * @param int|string $end   截止日期
     * @return array
     */
    public static function getStatsClient($begin, $end)
    {
        return [];
    }
}
