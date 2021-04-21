<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    通知模板解析
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/4/20
 */

namespace app\careyshop\event\service\notice;

use app\careyshop\model\User;
use app\careyshop\model\Notice as NoticeModel;
use think\helper\Str;

class Notice
{
    /**
     * @var Notice|null 当前实例
     */
    protected static ?Notice $instance = null;

    /**
     * @return Notice
     */
    public static function instance(): Notice
    {
        if (is_null(self::$instance))
            self::$instance = new self();

        return self::$instance;
    }

    /**
     * 发送通知
     * @access public
     * @param array $data 由事件触发所得数据
     * @param int   $code 事件编码
     * @throws
     */
    public function send(array $data, int $code)
    {
        if (empty($data['user_id']) && empty($data['admin_id'])) {
            abort(500, '发送通知时user_id或admin_id字段必须');
        }

        // 待发送数据
        $sendData = ['data' => $data, 'code' => $code];

        // 获取用户数据
        if (!empty($data['user_id'])) {
            $sendData['user'] = User::findOrEmpty($data['user_id'])
                ->append(['last_ip_region'])
                ->toArray();
        }

        // 获取通知模板
        $map[] = ['notice_event_id', '=', $code];
        $map[] = ['status', '=', 1];

        $result = NoticeModel::where($map)
            ->withoutField('status')
            ->select()
            ->toArray();

        // 生成发送实例并发送
        foreach ($result as $value) {
            $sendData['notice'] = $value;
            $value['place_id'] > 0 ? $this->sendPlatform($sendData) : $this->sendSystem($sendData);
        }
    }

    private function sendSystem(array $data)
    {
    }

    private function sendPlatform(array $data)
    {
    }
}
