<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    消息群发服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/11/3
 */

namespace app\careyshop\wechat\service\official_account;

use app\careyshop\wechat\service\CareyShop;

class Message extends CareyShop
{
    /**
     * 发送一条群发消息
     * @access public
     * @return array|false
     * @throws
     */
    public function sendMessageItem()
    {
        $type = $this->params['type'];
        $wechat = $this->getApp('broadcasting');

        // text时直接传入文本,其他需要传入素材ID
        $mediaId = $this->params['media_id'];
        $openId = $this->params['openid'] ?? [];
        $tagId = (int)$this->params['tag_id'];

        // 确定实际要发送的对象
        $to = !empty($openId) ? $openId : (!empty($tagId) ? $tagId : null);

        switch ($type) {
            case 'text':
                $result = $wechat->sendText($mediaId, $to);
                break;

            case 'news':
                $result = $wechat->sendNews($mediaId, $to);
                break;

            case 'image':
                $result = $wechat->sendImage($mediaId, $to);
                break;

            case 'voice':
                $result = $wechat->sendVoice($mediaId, $to);
                break;

            case 'video':
                $result = $wechat->sendVideo($mediaId, $to);
                break;

            default:
                return $this->setError('参数type只能在 text,news,image,voice,video 范围内');
        }

        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result;
    }

    /**
     * 发送预览消息给指定的微信用户或粉丝
     * @access public
     * @return bool
     * @throws
     */
    public function sendMessageView(): bool
    {
        $result = null;
        $type = $this->params['type'];
        $typeMap = ['text', 'news', 'image', 'voice', 'video'];

        if (!in_array($type, $typeMap)) {
            return $this->setError(sprintf('参数type只能在 %s 范围内', implode(',', $typeMap)));
        }

        // text时直接传入文本,其他需要传入素材ID
        $mediaId = $this->params['media_id'];
        $wxname = $this->params['wxname'];
        $openId = $this->params['open_id'];
        $wechat = $this->getApp('broadcasting');

        if ($wxname) {
            switch ($type) {
                case 'text':
                    $result = $wechat->previewTextByName($mediaId, $wxname);
                    break;

                case 'news':
                    $result = $wechat->previewNewsByName($mediaId, $wxname);
                    break;

                case 'image':
                    $result = $wechat->previewImageByName($mediaId, $wxname);
                    break;

                case 'voice':
                    $result = $wechat->previewVoiceByName($mediaId, $wxname);
                    break;

                case 'video':
                    $result = $wechat->previewVideoByName($mediaId, $wxname);
                    break;
            }
        } else if ($openId) {
            switch ($type) {
                case 'text':
                    $result = $wechat->previewText($mediaId, $openId);
                    break;

                case 'news':
                    $result = $wechat->previewNews($mediaId, $openId);
                    break;

                case 'image':
                    $result = $wechat->previewImage($mediaId, $openId);
                    break;

                case 'voice':
                    $result = $wechat->previewVoice($mediaId, $openId);
                    break;

                case 'video':
                    $result = $wechat->previewVideo($mediaId, $openId);
                    break;
            }
        } else {
            return $this->setError('参数"wxname"或"open_id"其中之一必填');
        }

        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return true;
    }

    /**
     * 删除一条群发消息
     * @access public
     * @return bool
     * @throws
     */
    public function delMessageItem(): bool
    {
        $result = $this->getApp('broadcasting')->delete($this->params['msg_id']);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return true;
    }

    /**
     * 查询群发消息发送状态
     * @access public
     * @return array|false
     * @throws
     */
    public function getMessageStatus()
    {
        $result = $this->getApp('broadcasting')->status($this->params['msg_id']);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result;
    }
}
