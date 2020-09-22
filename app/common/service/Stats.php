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

use app\common\model\Order;
use careyshop\Time;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Db;

class Stats extends CareyShop
{
    /**
     * 获取店铺统计数据
     * @access public
     * @return array
     * @throws
     */
    public static function getStatsShop()
    {
        // 缓存时间
        $expire = Config::get('careyshop.system_info.stats_time', 30) * 60;

        // 数据结构
        return Cache::remember('statsIndex', function () {
            $result = [
                // 今天
                'today'     => [
                    'order'    => 0,    // 订单数
                    'sales'    => 0,    // 销售额
                    'trade'    => 0,    // 已完成
                    'goods'    => 0,    // 商品数
                    'collect'  => 0,    // 收藏量
                    'client'   => 0,    // 会员数
                    'service'  => 0,    // 售后单
                    'withdraw' => 0,    // 提现单
                ],
                // 昨天
                'yesterday' => [
                    'order'    => 0,
                    'sales'    => 0,
                    'trade'    => 0,
                    'goods'    => 0,
                    'collect'  => 0,
                    'client'   => 0,
                    'service'  => 0,
                    'withdraw' => 0,
                ],
                // 本月订单量
                'order'     => [],
                // 本月会员数
                'client'    => [],
                // 本月热销TOP10
                'goods'     => [],
            ];

            $mapOrder = ['parent_id' => 0, 'is_delete' => 0];
            $mapTrade = ['trade_status' => 3];
            $mapGoods = ['is_delete' => 0];
            $mapUser = ['is_delete' => 0];

            $result['today']['order'] = Db::name('order')
                ->where($mapOrder)
                ->whereDay('create_time')
                ->count();

            $result['yesterday']['order'] = Db::name('order')
                ->where($mapOrder)
                ->whereDay('create_time', 'yesterday')
                ->count();

            $result['today']['sales'] = Db::name('order')
                ->where($mapOrder)
                ->whereDay('create_time')
                ->sum('pay_amount');

            $result['yesterday']['sales'] = Db::name('order')
                ->where($mapOrder)
                ->whereDay('create_time', 'yesterday')
                ->sum('pay_amount');

            $result['today']['trade'] = Db::name('order')
                ->where($mapOrder)
                ->where($mapTrade)
                ->whereDay('create_time')
                ->count();

            $result['yesterday']['trade'] = Db::name('order')
                ->where($mapOrder)
                ->where($mapTrade)
                ->whereDay('create_time', 'yesterday')
                ->count();

            $result['today']['goods'] = Db::name('goods')
                ->where($mapGoods)
                ->whereDay('create_time')
                ->count();

            $result['yesterday']['goods'] = Db::name('goods')
                ->where($mapGoods)
                ->whereDay('create_time', 'yesterday')
                ->count();

            $result['today']['collect'] = Db::name('collect')
                ->whereDay('create_time')
                ->count();

            $result['yesterday']['collect'] = Db::name('collect')
                ->whereDay('create_time', 'yesterday')
                ->count();

            $result['today']['client'] = Db::name('user')
                ->where($mapUser)
                ->whereDay('create_time')
                ->count();

            $result['yesterday']['client'] = Db::name('user')
                ->where($mapUser)
                ->whereDay('create_time', 'yesterday')
                ->count();

            $result['today']['service'] = Db::name('order_service')
                ->whereDay('create_time')
                ->count();

            $result['yesterday']['service'] = Db::name('order_service')
                ->whereDay('create_time', 'yesterday')
                ->count();

            $result['today']['withdraw'] = Db::name('withdraw')
                ->whereDay('create_time')
                ->count();

            $result['yesterday']['withdraw'] = Db::name('withdraw')
                ->whereDay('create_time', 'yesterday')
                ->count();

            $order = Db::name('order')
                ->field('FROM_UNIXTIME(create_time, "%c-%e") as day, count(*) as count')
                ->where($mapOrder)
                ->whereMonth('create_time')
                ->group('FROM_UNIXTIME(create_time, "%Y%c%e")')
                ->select()
                ->column('count', 'day');

            $client = Db::name('user')
                ->field('FROM_UNIXTIME(create_time, "%c-%e") as day, count(*) as count')
                ->where($mapUser)
                ->whereMonth('create_time')
                ->group('FROM_UNIXTIME(create_time, "%Y%c%e")')
                ->select()
                ->column('count', 'day');

            $result['goods'] = Db::name('goods')
                ->field('goods_id,name,short_name,sales_sum')
                ->where($mapGoods)
                ->order('sales_sum', 'desc')
                ->limit(10)
                ->select()
                ->toArray();

            $month = date('n');
            $dayCount = Time::daysMonth();

            for ($i = 0; $i <= $dayCount; $i++) {
                $key = sprintf('%s-%d', $month, $i + 1);
                if (($i + 1) > date('j')) {
                    break;
                }

                $result['order'][] = [
                    'day'   => $key,
                    'count' => array_key_exists($key, $order) ? $order[$key] : 0,
                ];

                $result['client'][] = [
                    'day'   => $key,
                    'count' => array_key_exists($key, $client) ? $client[$key] : 0,
                ];
            }

            return $result;
        }, $expire);
    }

    /**
     * 获取商品统计数据
     * @access public
     * @param int $begin 起始日期
     * @param int $end   截止日期
     * @return array
     * @throws
     */
    public static function getStatsGoods(int $begin, int $end)
    {
        // 缓存时间
        $expire = Config::get('careyshop.system_info.stats_time', 30) * 60;

        // 数据结构
        $result = Cache::remember('statsGoods', function () {
            $data = [
                // 今天
                'today' => [
                    'new'     => 0, // 新增数
                    'online'  => 0, // 在售数
                    'offline' => 0, // 仓库数
                    'views'   => 0, // 游览量
                    'sales'   => 0, // 销售数
                    'collect' => 0, // 收藏量
                ],
                // 趋势
                'chart' => [],
                // TOP10
                'top'   => [],
            ];

            $data['today']['new'] = Db::name('goods')
                ->where('status', '=', 1)
                ->where('is_delete', '=', 0)
                ->whereDay('create_time')
                ->count();

            $data['today']['online'] = Db::name('goods')
                ->where('status', '=', 1)
                ->where('is_delete', '=', 0)
                ->count();

            $data['today']['offline'] = Db::name('goods')
                ->where('status', '=', 0)
                ->where('is_delete', '=', 0)
                ->count();

            $data['today']['views'] = Db::name('goods')
                ->where('is_delete', '=', 0)
                ->whereDay('create_time')
                ->sum('page_views');

            $data['today']['sales'] = Db::name('goods')
                ->where('is_delete', '=', 0)
                ->whereDay('create_time')
                ->sum('sales_sum');

            $data['today']['collect'] = Db::name('collect')
                ->whereDay('create_time')
                ->count();

            return $data;
        }, $expire);

        $result['top'] = Db::name('goods')
            ->field('goods_id,name,short_name,sales_sum')
            ->where('is_delete', '=', 0)
            ->where('create_time', 'between time', [$begin, $end])
            ->order('sales_sum', 'desc')
            ->limit(10)
            ->select()
            ->toArray();

        $goods = Db::name('goods')
            ->field('FROM_UNIXTIME(create_time, "%Y-%m-%d") as day, SUM(sales_sum) as sales, SUM(page_views) as views')
            ->where('is_delete', '=', 0)
            ->where('create_time', 'between time', [$begin, $end])
            ->group('FROM_UNIXTIME(create_time, "%Y%m%d")')
            ->select()
            ->column(null, 'day');

        while ($begin <= $end) {
            $key = date('Y-m-d', ctype_digit($begin) ? (int)$begin : strtotime($begin));
            $begin += 86400;

            $result['chart'][] = array_key_exists($key, $goods)
                ? ['day' => $key, 'sales' => (int)$goods[$key]['sales'], 'views' => (int)$goods[$key]['views']]
                : ['day' => $key, 'sales' => 0, 'views' => 0];
        }

        return $result;
    }

    /**
     * 获取订单统计数据
     * @access public
     * @param int $begin 起始日期
     * @param int $end   截止日期
     * @return array
     * @throws
     */
    public static function getStatsOrder(int $begin, int $end)
    {
        // 缓存时间
        $expire = Config::get('careyshop.system_info.stats_time', 30) * 60;

        // 数据结构
        $result = Cache::remember('statsOrder', function () {
            $data = [
                'today' => [
                    'order'       => 0, // 订单数
                    'sales'       => 0, // 销售额
                    'not_paid'    => 0, // 待付款
                    'paid'        => 0, // 已付款
                    'not_shipped' => 0, // 待发货
                    'shipped'     => 0, // 已发货
                    'not_comment' => 0, // 待评价
                ],
                'chart' => [
                    'order'  => [],
                    'source' => [],
                ],
            ];

            $map = [
                ['parent_id', '=', 0],
                ['is_delete', '=', 0],
            ];

            $data['today']['order'] = Db::name('order')
                ->where($map)
                ->whereDay('create_time')
                ->count();

            $data['today']['sales'] = Db::name('order')
                ->where($map)
                ->whereDay('create_time')
                ->sum('pay_amount');

            $order = (new Order())->getOrderStatusTotal([]);
            $data['today'] = array_merge($data['today'], $order);

            return $data;
        }, $expire);

        $result['chart']['source'] = Db::name('order')
            ->field('source as name, COUNT(source) as count')
            ->where('is_delete', '=', 0)
            ->where('create_time', 'between time', [$begin, $end])
            ->group('source')
            ->select()
            ->toArray();

        $source = json_decode(Config::get('careyshop.system_shopping.source'), true);
        foreach ($result['chart']['source'] as &$item) {
            if (array_key_exists($item['name'], $source)) {
                $item['name'] = $source[$item['name']]['name'];
            }
        }

        $order = Db::name('order')
            ->field('FROM_UNIXTIME(create_time, "%Y-%m-%d") as day, COUNT(*) as count')
            ->where('is_delete', '=', 0)
            ->where('create_time', 'between time', [$begin, $end])
            ->group('FROM_UNIXTIME(create_time, "%Y%m%d")')
            ->select()
            ->column('count', 'day');

        $payment = Db::name('order')
            ->field('FROM_UNIXTIME(create_time, "%Y-%m-%d") as day, COUNT(*) as count')
            ->where('payment_status', '=', 1)
            ->where('is_delete', '=', 0)
            ->where('create_time', 'between time', [$begin, $end])
            ->group('FROM_UNIXTIME(create_time, "%Y%m%d")')
            ->select()
            ->column('count', 'day');

        while ($begin <= $end) {
            $key = date('Y-m-d', ctype_digit($begin) ? (int)$begin : strtotime($begin));
            $begin += 86400;

            $orderCount = array_key_exists($key, $order) ? $order[$key] : 0;
            $paymentCount = array_key_exists($key, $payment) ? $payment[$key] : 0;

            $result['chart']['order'][] = [
                'day'     => $key,
                'order'   => $orderCount,
                'payment' => $paymentCount,
                'percent' => $orderCount > 0 ? round($paymentCount / $orderCount, 2) : 0,
            ];
        }

        return $result;
    }

    /**
     * 获取会员统计数据
     * @access public
     * @param int $begin 起始日期
     * @param int $end   截止日期
     * @return array
     * @throws
     */
    public static function getStatsClient(int $begin, int $end)
    {
        // 缓存时间
        $expire = Config::get('careyshop.system_info.stats_time', 30) * 60;

        // 数据结构
        $result = Cache::remember('statsClient', function () {
            $data = [
                // 今天
                'today' => [
                    'count'   => 0, // 合计数
                    'enable'  => 0, // 启用数
                    'disable' => 0, // 禁用数
                    'new'     => 0, // 新增数
                    'active'  => 0, // 活动数
                ],
                // 趋势
                'chart' => [
                    'level' => [],
                    'login' => [],
                ],
            ];

            $data['today']['count'] = Db::name('user')
                ->where('is_delete', '=', 0)
                ->count();

            $data['today']['enable'] = Db::name('user')
                ->where('status', '=', 1)
                ->where('is_delete', '=', 0)
                ->count();

            $data['today']['disable'] = Db::name('user')
                ->where('status', '=', 0)
                ->where('is_delete', '=', 0)
                ->count();

            $data['today']['new'] = Db::name('user')
                ->where('is_delete', '=', 0)
                ->whereDay('create_time')
                ->count();

            $data['today']['active'] = Db::name('user')
                ->where('is_delete', '=', 0)
                ->whereDay('update_time')
                ->count();

            return $data;
        }, $expire);

        $result['chart']['level'] = Db::name('user')
            ->field('user_level_id, COUNT(user_level_id) as count')
            ->where('is_delete', '=', 0)
            ->cache(true, $expire)
            ->group('user_level_id')
            ->select()
            ->toArray();

        $levels = Db::name('user_level')->column('name', 'user_level_id');
        foreach ($result['chart']['level'] as &$item) {
            if (array_key_exists($item['user_level_id'], $levels)) {
                $item['name'] = $levels[$item['user_level_id']];
            }

            unset($item['user_level_id']);
        }

        $login = Db::name('user')
            ->field('FROM_UNIXTIME(create_time, "%Y-%m-%d") as day, COUNT(*) as count')
            ->where('is_delete', '=', 0)
            ->where('create_time', 'between time', [$begin, $end])
            ->group('FROM_UNIXTIME(create_time, "%Y%m%d")')
            ->select()
            ->column(null, 'day');

        while ($begin <= $end) {
            $key = date('Y-m-d', ctype_digit($begin) ? (int)$begin : strtotime($begin));
            $begin += 86400;

            $result['chart']['login'][] = array_key_exists($key, $login)
                ? $login[$key]
                : ['day' => $key, 'count' => 0];
        }

        return $result;
    }
}
