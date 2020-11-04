<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    自动回复服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/29
 */

namespace app\common\wechat\service;

use think\facade\Cache;

class Reply extends CareyShop
{
    /**
     * 公众号用户缓存标识
     * @var string
     */
    const WECHAT_REPLY = 'WechatReply';

    /**
     * 获取自动回复配置
     * @access public
     * @return array
     */
    public function getReplyData()
    {
        $cacheKey = self::WECHAT_REPLY . $this->params['code'];
        $result = Cache::get($cacheKey, []);

        if (array_key_exists($this->params['type'], $result)) {
            return $result[$this->params['type']];
        }

        return [];
    }

    /**
     * 设置自动回复配置
     * @access public
     * @return bool
     */
    public function setReplyData()
    {
        // 数据结构
//        $data = [
//            // 关注回复
//            'subscribe' => [
//                'type'     => 'text',       // 文本=text 图片=image 语音=voice 视频=video 图文=news
//                'media_id' => [],           // 素材编号(>1 随机)
//                'status'   => 1,            // 0=禁用 1=启用
//            ],
//            // 关键词回复
//            'keyword'   => [
//                [
//                    'keyword'  => [],       // 关键词
//                    'mode'     => 0,        // 0=模糊匹配 1=完全匹配
//                    'type'     => 'text',   // 文本=text 图片=image 语音=voice 视频=video 图文=news
//                    'media_id' => [],       // 素材编号(>1 随机)
//                    'status'   => 1,        // 0=禁用 1=启用
//                ],
//                // ...更多的关键词
//            ],
//            // 默认回复
//            'default'   => [
//                'type'     => 'text',       // 文本=text 图片=image 语音=voice 视频=video 图文=news
//                'media_id' => [],           // 素材编号(>1 随机)
//                'status'   => 1,            // 0=禁用 1=启用
//            ],
//        ];
        $type = $this->params['type'];
        $setting = $this->params['setting'] ?? [];

        if (!is_array($setting)) {
            return false;
        }

        $cacheKey = self::WECHAT_REPLY . $this->params['code'];
        $cacheData = Cache::get($cacheKey, []);

        empty($type) ? $cacheData[$type] = $setting : $cacheData = $setting;
        Cache::set($cacheKey, $cacheData);

        return true;
    }
}
