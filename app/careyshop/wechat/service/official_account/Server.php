<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    消息服务端服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/20
 */

namespace app\careyshop\wechat\service\official_account;

use app\careyshop\wechat\Params;
use app\careyshop\wechat\service\CareyShop;
use EasyWeChat\Kernel\Messages\Media;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Transfer;
use think\facade\Cache;

class Server extends CareyShop
{
    /**
     * 接收并响应微信推送
     * @access public
     * @return string[]
     * @throws
     */
    public function putWechatData(): array
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

        // 响应实际输出
        $server->serve()->send();

        // 必须返回,否则将导致后续执行(如日志)中断
        return [
            'callback_return_type' => 'view',
            'is_callback'          => 'success',
        ];
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
            switch ($res['Event']) {
                // 订阅
                case 'subscribe':
                    $cacheKey = User::WECHAT_USER . $this->params['code'];
                    $userList = Cache::store('place')->get($cacheKey, []);

                    array_unshift($userList, $res['FromUserName']);
                    Cache::store('place')->set($cacheKey, $userList);

                    // 订阅回复
                    return $this->getReplyContent('subscribe');

                // 取消订阅
                case 'unsubscribe':
                    $cacheKey = User::WECHAT_USER . $this->params['code'];
                    $userList = Cache::store('place')->get($cacheKey, []);
                    $key = array_search($res['FromUserName'], $userList);

                    if (false !== $key) {
                        unset($userList[$key]);
                        Cache::store('place')->set($cacheKey, $userList);
                    }
                    break;

                case 'CLICK':
                    // 菜单点击事件,事件处理预留
                    break;

                case 'weapp_audit_success':
                    // 小程序审核通过,事件处理预留
                    break;

                //...更多事件,自行处理
            }

            return null;
        });
    }

    /**
     * 处理文字消息
     * @access private
     * @return void
     * @throws
     */
    private function handleText()
    {
        $this->getApp('server')->push(function ($res) {
            // 处理关键词回复
            $cacheKey = Reply::WECHAT_REPLY . $this->params['code'];
            $cacheData = Cache::store('place')->get($cacheKey, []);

            if (array_key_exists('keyword', $cacheData)) {
                foreach ($cacheData['keyword'] as $value) {
                    if (0 == $value['mode'] && false !== mb_stristr($res['Content'], $value['keyword'])) {
                        return $this->getReplyContent($value);
                    }

                    if (1 == $value['mode'] && 0 === strnatcasecmp($res['Content'], $value['keyword'])) {
                        return $this->getReplyContent($value);
                    }
                }
            }

            // 处理默认回复
            $cacheKey .= 'default';
            if (!Cache::store('place')->get($cacheKey)) {
                $defaultReply = $this->getReplyContent('default');
                if (!empty($defaultReply)) {
                    Cache::store('place')->set($cacheKey, true, 3600);
                    return $defaultReply;
                }
            }

            // 处理转发客服系统
            if ($this->expand['is_transfer']) {
                return new Transfer($this->expand['transfer_account']);
            }

            return null;
        });
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
    public function clearWechatQuota(): bool
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
     * @param array|string $source 数据源
     * @return Media|Text|void
     * @throws
     */
    private function getReplyContent($source)
    {
        // 将数据转为对象访问
        if (is_array($source)) {
            $data = new Params($source);
        } else {
            $cacheKey = Reply::WECHAT_REPLY . $this->params['code'];
            $cacheData = Cache::store('place')->get($cacheKey, []);

            if (!array_key_exists($source, $cacheData)) {
                return;
            }

            $data = new Params($cacheData[$source]);
        }

        // 检测状态是否可用
        if (!$data['status']) {
            return;
        }

        // 检测是否有可回复素材
        if (empty($data['media_id'])) {
            return;
        }

        // 获取回复素材(数组大于1时随机取)
        $index = array_rand($data['media_id']);
        $mediaId = $data['media_id'][$index];

        switch ($data['type']) {
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
                return;
        }

        return $result;
    }
}
