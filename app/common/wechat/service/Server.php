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
        $this->getApp('server')->push(function ($message) {
            if (!isset($message['MsgType'])) {
                return null;
            }

            switch ($message['MsgType']) {
                case 'event':
                    //return '收到事件消息';
                    break;

                case 'text':
                    //return '收到文字消息';
                    break;

                case 'image':
                    //return '收到图片消息';
                    break;

                case 'voice':
                    //return '收到语音消息';
                    break;

                case 'video':
                    //return '收到视频消息';
                    break;

                case 'location':
                    //return '收到坐标消息';
                    break;

                case 'link':
                    //return '收到链接消息';
                    break;

                case 'file':
                    //return '收到文件消息';
                    break;

                default:
                    //return '收到其它消息';
                    break;
            }
        });

        $this->getApp('server')->serve()->send();
        exit();
    }
}
