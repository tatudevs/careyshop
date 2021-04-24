<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    系统通知驱动
 *
 * @author      zxm <252404501@qq.com>
 * @version     v1.1
 * @date        2021/4/20
 */

namespace app\careyshop\event\service\notice\driver;

use app\careyshop\event\service\notice\Driver;
use think\facade\Config;
use util\Notice;

class System extends Driver
{
    /**
     * 发送通知
     * @access public
     * @param array $params 事件外部数据
     */
    public function send(array $params)
    {
        // 解析外部数据成变量
        [
            'data'     => $this->data,      // 订阅者提供数据
            'code'     => $this->code,      // 事件编码(Base)
            'user'     => $this->user,      // 事件对应账号数据
            'variable' => $this->variable,  // 宏替换变量
            'notice'   => $this->notice,    // 通知数据结构
        ] = $params;

        // 对订阅者提供的数据进行补齐
        $this->getPaddedData();

        // 检查通知是否启用
        $setting = json_decode(Config::get('careyshop.notice.' . $this->notice['type'], 0), true);
        if ((int)$setting['status']['value'] <= 0) {
            return;
        }

        // 检查发送的类型
        if (!in_array($this->notice['type'], ['sms', 'email'])) {
            return;
        }

        if ($this->notice['type'] == 'sms' && empty($this->user['mobile'])) {
            return;
        }

        if ($this->notice['type'] == 'email' && empty($this->user['email'])) {
            return;
        }

        // 从模板中获取宏变量名
        $macroItem = [];
        if (!preg_match_all('/{([^}]+)}/', $this->notice['expand']['template'], $macroItem)) {
            return;
        }

        // 实际发送
        if ($this->notice['type'] == 'sms') {
            foreach ($macroItem[0] as $value) {
                if (!isset($this->variable[$value])) {
                    continue;
                }

                if (!isset($this->data[$this->variable[$value]])) {
                    continue;
                }

                $body[$this->variable[$value]] = $this->data[$this->variable[$value]];
            }

            $body = json_encode($body ?? [], JSON_UNESCAPED_UNICODE);
            ['code' => $code, 'sign' => $sign] = $this->notice['expand'];
            Notice::sendSms($this->user['mobile'], $body, $code, $sign);
        }

        if ($this->notice['type'] == 'email') {
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

            ['title' => $title, 'template' => $template] = $this->notice['expand'];
            $body = str_replace($search ?? [], $replace ?? [], $template);
            Notice::sendEmail($this->user['email'], $title, $body);
        }
    }
}
