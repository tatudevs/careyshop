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

    public function send(array $data, int $code)
    {
        if (empty($data['user_id']) && empty($data['admin_id'])) {
            abort(500, '发送通知时user_id或admin_id字段必须');
        }

        // 待发送数据
        $sendData = ['data' => $data, 'key' => $code];

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
        $namespace = __NAMESPACE__;
        print_r($namespace);exit();

//        foreach ($result as $value) {
//        }
    }
}
