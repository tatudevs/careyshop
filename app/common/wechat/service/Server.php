<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    消息服务端服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/20
 */

namespace app\common\wechat\service;

use app\common\wechat\Params;
use EasyWeChat\Kernel\Messages\Media;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Transfer;
use think\facade\Cache;

class Server extends CareyShop
{
    /**
     * 接收并响应微信推送
     * @access public
     * @return void
     * @throws
     */
    public function putWechatData()
    {
        // 获取消息
        $server = $this->getApp('server');
        $message = $server->getMessage();

        if (isset($message['MsgType'])) {
            switch ($message['MsgType']) {
                // 事件消息
                case 'event':
                    $this->handleEvent();
                    break;

                // 文字消息
                case 'text':
                    $this->handleText();
                    break;

                // 图片消息
                case 'image':
                    $this->handleImage();
                    break;

                // 语音消息
                case 'voice':
                    $this->handleVoice();
                    break;

                // 视频消息
                case 'video':
                    $this->handleVideo();
                    break;

                // 坐标消息
                case 'location':
                    $this->handleLocation();
                    break;

                // 链接消息
                case 'link':
                    $this->handleLink();
                    break;

                // 文件消息
                case 'file':
                    $this->handleFile();
                    break;

                // 其它消息
                default:
                    $this->handleOther($message['MsgType']);
            }
        }

        // 转发消息至客服系统
//        $server->push(function () {
//            return new Transfer();
//        });

        // 响应实际输出
        $server->serve()->send();
        exit();
    }

    /**
     * 处理事件消息
     * @access private
     * @return void
     * @throws
     */
    private function handleEvent()
    {
        $this->getApp('server')->push(function ($res) {
            // 订阅
            if ($res['Event'] == 'subscribe') {
                $cacheKey = User::WECHAT_USER . $this->params['code'];
                $userList = Cache::store('place')->get($cacheKey, []);

                array_unshift($userList, $res['FromUserName']);
                Cache::store('place')->set($cacheKey, $userList);

                // 订阅回复
                return $this->getSubscribeReply($this->params['code']);
            }

            // 取消订阅
            if ($res['Event'] == 'unsubscribe') {
                $cacheKey = User::WECHAT_USER . $this->params['code'];
                $userList = Cache::store('place')->get($cacheKey, []);
                $key = array_search($res['FromUserName'], $userList);

                if (false !== $key) {
                    unset($userList[$key]);
                    Cache::store('place')->set($cacheKey, $userList);
                }
            }

            return null;
        });
    }

    /**
     * 处理文字消息
     * @access private
     * @return void
     */
    private function handleText()
    {
    }

    /**
     * 处理图片消息
     * @access private
     * @return void
     */
    private function handleImage()
    {
    }

    /**
     * 处理语音消息
     * @access private
     * @return void
     */
    private function handleVoice()
    {
    }

    /**
     * 处理视频消息
     * @access private
     * @return void
     */
    private function handleVideo()
    {
    }

    /**
     * 处理坐标消息
     * @access private
     * @return void
     */
    private function handleLocation()
    {
    }

    /**
     * 处理链接消息
     * @access private
     * @return void
     */
    private function handleLink()
    {
    }

    /**
     * 处理文件消息
     * @access private
     * @return void
     */
    private function handleFile()
    {
    }

    /**
     * 处理其它消息
     * @access private
     * @param string $type 消息类型
     * @return void
     */
    private function handleOther(string $type)
    {
    }

    /**
     * 长链接转短链接
     * @access public
     * @return array|false
     * @throws
     */
    public function setWechatShort()
    {
        $result = $this->getApp('url')->shorten($this->params['url']);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result['short_url'];
    }

    /**
     * 获取微信服务器IP(或IP段)
     * @access public
     * @return array|false
     * @throws
     */
    public function getWechatIP()
    {
        $result = $this->getApp('base')->getValidIps();
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result['ip_list'];
    }

    /**
     * 清理接口调用次数(每月10次)
     * @access public
     * @return bool
     * @throws
     */
    public function clearWechatQuota()
    {
        $result = $this->getApp('base')->clearQuota();
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return true;
    }

    /**
     * 从回复规则中获取回复内容
     * @access private
     * @param array $source 数据源
     * @return array|Media|Text|void
     */
    private function getReplyContent(array $source)
    {
        // 将数据转为对象访问
        $result = [];
        $data = new Params($source);

        // 检测状态是否可用
        if (!$data['status']) {
            return;
        }

        // 获取多媒体素材
        $getMedia = function ($type, $mediaId) {
            switch ($type) {
                case 'image':
                    $result = new Media($mediaId, 'image');
                    break;

                case 'voice':
                    $result = new Media($mediaId, 'voice');
                    break;

                case 'video':
                    $result = new Media($mediaId, 'mpvideo');
                    break;

                case 'news':
                    $result = new Media($mediaId, 'mpnews');
                    break;

                case 'text':
                    $result = new Text($mediaId);
                    break;

                default:
                    return null;
            }

            return $result;
        };

        // 检测是否有可回复素材
        $key = isset($data['keyword']) ? 'value' : 'media_id';
        if (empty($data[$key])) {
            return;
        }

        // 获取回复素材(数组大于1时随机取)
        $index = array_rand($data[$key], 1);
        $mediaId = $data[$key][$index];

        if (isset($data['keyword'])) {
            // 处理关键词回复
            if (0 == $data['type']) {
                $result = $getMedia($mediaId['type'], $mediaId['media_id']);
            } else if (1 == $data['type']) {
                foreach ($data[$key] as $item) {
                    $result[] = $getMedia($item['type'], $item['media_id']);
                }
            }
        } else {
            $result = $getMedia($data['type'], $mediaId);
        }

        return $result;
    }

    /**
     * 关注后获取回复内容
     * @access private
     * @param string $code 渠道编号
     * @return Media|Text|void
     * @throws
     */
    private function getSubscribeReply(string $code)
    {
        // 获取数据并检测配置是否存在
        $cacheKey = Reply::WECHAT_REPLY . $code;
        $cacheData = Cache::store('place')->get($cacheKey, []);

        if (!array_key_exists('subscribe', $cacheData)) {
            return;
        }

        // 获取可回复素材
        $result = $this->getReplyContent($cacheData['subscribe']);
        if (!is_object($result)) {
            return;
        }

        return $result;
    }
}
