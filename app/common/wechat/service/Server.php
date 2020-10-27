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

use think\facade\Cache;

class Server extends CareyShop
{
    /**
     * 接收并响应微信推送
     * @access public
     * @return void
     * @throws
     */
    public function putWeChatData()
    {
        // 获取消息
        $message = $this->getApp('server')->getMessage();
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
                    $this->handleOther();
            }
        }

        // todo 消息转发给客服未实现

        // 响应实际输出
        $this->getApp('server')->serve()->send();
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
                $cacheKey = self::WECHAT_USER . $this->params['code'];
                $userList = Cache::get($cacheKey, []);

                array_unshift($userList, $res['FromUserName']);
                Cache::set($cacheKey, $userList);
                return;
            }

            // 取消订阅
            if ($res['Event'] == 'unsubscribe') {
                $cacheKey = self::WECHAT_USER . $this->params['code'];
                $userList = Cache::get($cacheKey, []);
                $key = array_search($res['FromUserName'], $userList);

                if (false !== $key) {
                    unset($userList[$key]);
                    Cache::set($cacheKey, $userList);
                }

                return;
            }
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
     * @return void
     */
    private function handleOther()
    {
    }
}
