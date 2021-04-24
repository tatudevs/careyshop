<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    数据统计服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/26
 */

namespace app\careyshop\wechat\service\official_account;

use app\careyshop\wechat\service\CareyShop;

class DataCube extends CareyShop
{
    /**
     * 获取公众号数据统计分析
     * @access public
     * @return array|false
     * @throws
     */
    public function getDataCube()
    {
        $type = $this->params['type'];
        $from = empty($this->params['from']) ? date('Y-m-d', strtotime('-2 day')) : $this->params['from'];
        $to = empty($this->params['to']) ? date('Y-m-d', strtotime('-1 day')) : $this->params['to'];

        $function = [
            'user_summary'                  => 'userSummary',
            'user_cumulate'                 => 'userCumulate',
            'article_summary'               => 'articleSummary',
            'article_Total'                 => 'articleTotal',
            'user_read_summary'             => 'userReadSummary',
            'user_read_hourly'              => 'userReadHourly',
            'user_share_summary'            => 'userShareSummary',
            'user_share_hourly'             => 'userShareHourly',
            'upstream_message_summary'      => 'upstreamMessageSummary',
            'upstream_message_hourly'       => 'upstreamMessageHourly',
            'upstream_message_weekly'       => 'upstreamMessageWeekly',
            'upstream_message_monthly'      => 'upstreamMessageMonthly',
            'upstream_message_dist_summary' => 'upstreamMessageDistSummary',
            'upstream_message_dist_weekly'  => 'upstreamMessageDistWeekly',
            'upstream_message_dist_monthly' => 'upstreamMessageDistMonthly',
            'interface_summary'             => 'interfaceSummary',
            'interface_summary_hourly'      => 'interfaceSummaryHourly',
        ];

        if (empty($type) || !array_key_exists($type, $function)) {
            return $this->setError('参数type必填，并且需在范围内：' . implode(',', array_keys($function)));
        }

        $getData = $function[$type];
        $result = $this->getApp('data_cube')->$getData($from, $to);

        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result;
    }
}
