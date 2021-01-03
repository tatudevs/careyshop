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
     * 获取后台统计数据
     * @access public
     * @return array
     * @throws
     */
    public static function getStatsIndex(): array
    {
        // 缓存时间
        $expire = Config::get('careyshop.system_info.stats_time', 30);

        // 数据结构
        return Cache::remember('statsIndex', function () {
            $result = [
                // 今日数据
                'today_data'    => [
                    'order'    => 0,    // 订单数
                    'trade'    => 0,    // 完成数
                    'collect'  => 0,    // 收藏量
                    'client'   => 0,    // 会员数
                    'service'  => 0,    // 售后单
                    'withdraw' => 0,    // 提现单
                    'comment'  => 0,    // 评价待回复
                    'consult'  => 0,    // 咨询待回复
                ],
                // 订单状态
                'order_status'  => [],
                // 订单来源比例
                'order_source'  => [],
                // 今日订单量(24H)
                'order_today'   => [],
                // 今日活跃会员数(24H)
                'client_active' => [],
                // 会员等级比例
                'client_level'  => [],
                // 销售状态
                'sales_status'  => [
                    // 昨日订单量及销售额
                    'yesterday' => [],
                    // 本月订单量及销售额
                    'month'     => [],
                ],
                // 商品排行榜
                'goods_top'     => [],
                // 统计时间
                'update_time'   => date('Y-m-d H:i:s'),
            ];

            $result['today_data']['order'] = Db::name('order')
                ->where(['parent_id' => 0, 'is_delete' => 0])
                ->whereDay('create_time')
                ->count();

            $result['today_data']['trade'] = Db::name('order')
                ->where(['parent_id' => 0, 'is_delete' => 0, 'trade_status' => 3])
                ->whereDay('finished_time')
                ->count();

            $result['today_data']['collect'] = Db::name('collect')
                ->whereDay('create_time')
                ->count();

            $result['today_data']['client'] = Db::name('user')
                ->where('is_delete', '=', 0)
                ->whereDay('create_time')
                ->count();

            $result['today_data']['service'] = Db::name('order_service')
                ->whereDay('create_time')
                ->count();

            $result['today_data']['withdraw'] = Db::name('withdraw')
                ->whereDay('create_time')
                ->count();

            $result['today_data']['comment'] = Db::name('goods_comment')
                ->where(['status' => 0, 'is_delete' => 0])
                ->whereDay('create_time')
                ->count();

            $result['today_data']['consult'] = Db::name('goods_consult')
                ->where(['status' => 0, 'is_delete' => 0])
                ->whereDay('create_time')
                ->count();

            $orderDB = new Order();
            $result['order_status'] = $orderDB->getOrderStatusTotal([]);

            $result['order_source'] = Db::name('order')
                ->field('source as name, COUNT(source) as count')
                ->where(['parent_id' => 0, 'is_delete' => 0])
                ->whereDay('create_time')
                ->group('source')
                ->select()
                ->toArray();

            $source = json_decode(Config::get('careyshop.system_shopping.source'), true);
            foreach ($result['order_source'] as &$item) {
                if (array_key_exists($item['name'], $source)) {
                    $item['name'] = $source[$item['name']]['name'];
                }
            }

            $orderToday = Db::name('order')
                ->field('FROM_UNIXTIME(create_time, "%k") as hour, count(*) as count')
                ->where(['parent_id' => 0, 'is_delete' => 0])
                ->whereDay('create_time')
                ->group('FROM_UNIXTIME(create_time, "%k")')
                ->select()
                ->column('count', 'hour');

            $clientActive = Db::name('user')
                ->field('FROM_UNIXTIME(update_time, "%k") as hour, count(*) as count')
                ->where('is_delete', '=', 0)
                ->whereDay('update_time')
                ->group('FROM_UNIXTIME(update_time, "%k")')
                ->select()
                ->column('count', 'hour');

            for ($i = 0; $i < 24; $i++) {
                $hour = str_pad($i, 2, '0', STR_PAD_LEFT);

                $result['order_today'][] = [
                    'hour'  => $hour,
                    'count' => array_key_exists($i, $orderToday) ? $orderToday[$i] : 0,
                ];

                $result['client_active'][] = [
                    'hour'  => $hour,
                    'count' => array_key_exists($i, $clientActive) ? $clientActive[$i] : 0,
                ];
            }

            $result['client_level'] = Db::name('user')
                ->field('user_level_id, COUNT(user_level_id) as count')
                ->where('is_delete', '=', 0)
                ->group('user_level_id')
                ->select()
                ->toArray();

            $levels = Db::name('user_level')->column('name', 'user_level_id');
            foreach ($result['client_level'] as &$item) {
                if (array_key_exists($item['user_level_id'], $levels)) {
                    $item['name'] = $levels[$item['user_level_id']];
                }

                unset($item['user_level_id']);
            }

            $result['sales_status']['yesterday'] = Db::name('order')
                ->field('COUNT(*) as count, SUM(pay_amount) as sales')
                ->where(['parent_id' => 0, 'is_delete' => 0])
                ->whereDay('create_time', 'yesterday')
                ->findOrEmpty();

            $result['sales_status']['month'] = Db::name('order')
                ->field('COUNT(*) as count, SUM(pay_amount) as sales')
                ->where(['parent_id' => 0, 'is_delete' => 0])
                ->whereMonth('create_time')
                ->findOrEmpty();

            $result['goods_top'] = Db::name('goods')
                ->field('goods_id,name,short_name,sales_sum')
                ->where('is_delete', '=', 0)
                ->order(['sales_sum' => 'desc', 'update_time' => 'desc'])
                ->limit(10)
                ->select()
                ->toArray();

            return $result;
        }, $expire * 60);
    }

    /**
     * 获取店铺统计数据
     * @access public
     * @return array
     * @throws
     */
    public static function getStatsShop(): array
    {
        // 缓存时间
        $expire = Config::get('careyshop.system_info.stats_time', 30);

        // 数据结构
        return Cache::remember('statsShop', function () {
            $result = [
                // 今天
                'today'        => [
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
                'yesterday'    => [
                    'order'    => 0,
                    'sales'    => 0,
                    'trade'    => 0,
                    'goods'    => 0,
                    'collect'  => 0,
                    'client'   => 0,
                    'service'  => 0,
                    'withdraw' => 0,
                ],
                // 今、昨24小时订单量
                'order_hours'  => [],
                // 本月订单量
                'order_month'  => [],
                // 本月会员数
                'client_month' => [],
                // 热销TOP10
                'goods'        => [],
                // 今日实时统计时间
                'update_time'  => date('Y-m-d H:i:s'),
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
                ->whereDay('finished_time')
                ->count();

            $result['yesterday']['trade'] = Db::name('order')
                ->where($mapOrder)
                ->where($mapTrade)
                ->whereDay('finished_time', 'yesterday')
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
                ->order(['sales_sum' => 'desc', 'update_time' => 'desc'])
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

                $result['order_month'][] = [
                    'day'   => $key,
                    'count' => array_key_exists($key, $order) ? $order[$key] : 0,
                ];

                $result['client_month'][] = [
                    'day'   => $key,
                    'count' => array_key_exists($key, $client) ? $client[$key] : 0,
                ];
            }

            $orderToday = Db::name('order')
                ->field('FROM_UNIXTIME(create_time, "%k") as hour, count(*) as count')
                ->where($mapOrder)
                ->whereDay('create_time')
                ->group('FROM_UNIXTIME(create_time, "%k")')
                ->select()
                ->column('count', 'hour');

            $orderYesterday = Db::name('order')
                ->field('FROM_UNIXTIME(create_time, "%k") as hour, count(*) as count')
                ->where($mapOrder)
                ->whereDay('create_time', 'yesterday')
                ->group('FROM_UNIXTIME(create_time, "%k")')
                ->select()
                ->column('count', 'hour');

            for ($i = 0; $i < 24; $i++) {
                $result['order_hours'][] = [
                    'hour'      => str_pad($i, 2, '0', STR_PAD_LEFT),
                    'today'     => array_key_exists($i, $orderToday) ? $orderToday[$i] : 0,
                    'yesterday' => array_key_exists($i, $orderYesterday) ? $orderYesterday[$i] : 0,
                ];
            }

            return $result;
        }, $expire * 60);
    }

    /**
     * 获取商品统计数据
     * @access public
     * @param int $begin 起始日期
     * @param int $end   截止日期
     * @return array
     * @throws
     */
    public static function getStatsGoods(int $begin, int $end): array
    {
        // 缓存时间
        $expire = Config::get('careyshop.system_info.stats_time', 30);

        // 数据结构
        $result = Cache::remember('statsGoods', function () {
            $data = [
                // 今天
                'today'       => [
                    'new'     => 0, // 新增数
                    'online'  => 0, // 在售数
                    'offline' => 0, // 仓库数
                    'views'   => 0, // 游览量
                    'sales'   => 0, // 销售数
                    'collect' => 0, // 收藏量
                ],
                // 趋势
                'chart'       => [],
                // TOP10
                'top'         => [],
                // 今日实时统计时间
                'update_time' => date('Y-m-d H:i:s'),
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
        }, $expire * 60);

        $result['top'] = Db::name('goods')
            ->field('goods_id,name,short_name,sales_sum')
            ->where('is_delete', '=', 0)
            ->where('update_time', 'between time', [$begin, $end])
            ->order('sales_sum', 'desc')
            ->limit(10)
            ->select()
            ->toArray();

        $goods = Db::name('goods')
            ->field('FROM_UNIXTIME(create_time, "%Y-%m-%d") as day, SUM(sales_sum) as sales, SUM(page_views) as views')
            ->where('is_delete', '=', 0)
            ->where('update_time', 'between time', [$begin, $end])
            ->group('FROM_UNIXTIME(create_time, "%Y%m%d")')
            ->select()
            ->column(null, 'day');

        while ($begin <= $end) {
            $key = date('Y-m-d', ctype_digit($begin) ? (int)$begin : strtotime($begin));
            $begin += 86400;

            $item = array_key_exists($key, $goods)
                ? ['day' => $key, 'sales' => (int)$goods[$key]['sales'], 'views' => (int)$goods[$key]['views']]
                : ['day' => $key, 'sales' => 0, 'views' => 0];

            $item['conversion'] = $item['views'] > 0 ? round($item['sales'] / $item['views'], 2) : 0;
            $result['chart'][] = $item;
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
    public static function getStatsOrder(int $begin, int $end): array
    {
        // 缓存时间
        $expire = Config::get('careyshop.system_info.stats_time', 30);

        // 数据结构
        $result = Cache::remember('statsOrder', function () {
            $data = [
                'today'       => [
                    'not_paid'    => 0, // 待付款
                    'paid'        => 0, // 已付款
                    'not_shipped' => 0, // 待发货
                    'shipped'     => 0, // 已发货
                    'not_comment' => 0, // 待评价
                    'order'       => 0, // 订单数
                    'sales'       => 0, // 销售额
                    'service'     => 0, // 售后单
                ],
                'chart'       => [
                    'order'  => [],
                    'source' => [],
                ],
                // 今日实时统计时间
                'update_time' => date('Y-m-d H:i:s'),
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

            $data['today']['service'] = Db::name('order_service')
                ->whereDay('create_time')
                ->count();

            $order = (new Order())->getOrderStatusTotal([]);
            $data['today'] = array_merge($data['today'], $order);

            return $data;
        }, $expire * 60);

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
    public static function getStatsClient(int $begin, int $end): array
    {
        // 缓存时间
        $expire = Config::get('careyshop.system_info.stats_time', 30);

        // 数据结构
        $result = Cache::remember('statsClient', function () {
            $data = [
                // 今天
                'today'       => [
                    'count'   => 0, // 合计数
                    'enable'  => 0, // 启用数
                    'disable' => 0, // 禁用数
                    'new'     => 0, // 新增数
                    'active'  => 0, // 活动数
                ],
                // 趋势
                'chart'       => [
                    'level' => [],
                    'login' => [],
                ],
                // 今日实时统计时间
                'update_time' => date('Y-m-d H:i:s'),
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
        }, $expire * 60);

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
