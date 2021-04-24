<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    微信通知驱动
 *
 * @author      zxm <252404501@qq.com>
 * @version     v1.1
 * @date        2021/4/22
 */

namespace app\careyshop\event\service\notice\driver;

use app\careyshop\event\service\notice\Driver;
use app\careyshop\model\PlaceOauth;
use app\careyshop\model\PlaceUser;

class Wechat extends Driver
{
    /**
     * 发送通知
     * @access public
     * @throws
     */
    public function send()
    {
        // 获取渠道用户数据
        $userMap[] = ['user_id', '=', $this->user['user_id']];
        $userMap[] = ['module', '=', 'wechat'];

        $placeUser = PlaceUser::where($userMap)->find();
        if (is_null($placeUser)) {
            return;
        }

        // 根据用户获取渠道配置
        $placeMap[] = ['place_oauth.place_oauth_id', '=', $placeUser['place_oauth_id']];
        $placeMap[] = ['place_oauth.module', '=', $placeUser['module']];
        $placeMap[] = ['place_oauth.status', '=', 1];
        $placeMap[] = ['getPlace.status', '=', 1];

        $placeDb = PlaceOauth::withJoin('getPlace')
            ->cache(true, null, 'oauth')
            ->where($placeMap)
            ->find();

        if (is_null($placeDb)) {
            return;
        }

        // 从模板中获取宏变量名
        $macroItem = [];
        $template  = json_encode($this->notice['expand']['data'], JSON_UNESCAPED_UNICODE);

        if (!preg_match_all('/{{([^}]+)}}/', $template, $macroItem)) {
            return;
        }

        foreach ($macroItem[0] as $value) {
            if (!isset($this->variable[$value])) {
                continue;
            }

            if (!isset($this->data[$this->variable[$value]])) {
                continue;
            }

            $search[] = $value;
            $replace[] = $this->data[$this->variable[$value]];
        }

        $template = str_replace($search ?? [], $replace ?? [], $template);
        $this->notice['expand']['touser'] = $placeUser['openid'];
        $this->notice['expand']['data'] = json_decode($template, true);

        $wechat = new \app\careyshop\wechat\WeChat($placeDb['getPlace']['code']);
        $wechat->getApp()->template_message->send($this->notice['expand']);
    }
}
