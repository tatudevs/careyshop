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

use app\careyshop\model\{NoticeVariable, User, Notice as NoticeModel};
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

        // 获取宏替换变量
        $sendData['variable'] = NoticeVariable::cache()
            ->where('notice_event_id', '=', $code)
            ->column('replace_name', 'item_name');

        // 获取通知模板
        $map[] = ['notice_event_id', '=', $code];
        $map[] = ['status', '=', 1];

        $result = NoticeModel::where($map)
            ->withoutField('status')
            ->select()
            ->toArray();

        // 生成发送实例并发送
        foreach ($result as $value) {
            $this->sendNotice($sendData, $value);
        }
    }

    /**
     * 调用驱动模块分发通知
     * @access private
     * @param array $data   待用数据
     * @param array $notice 通知数据
     */
    private function sendNotice(array $data, array $notice)
    {
        // 命名空间
        $namespace = __NAMESPACE__ . '\\driver\\';
        $namespace .= Str::title($notice['platform']);

        if (!class_exists($namespace)) {
            return;
        }

        if (!method_exists($namespace, 'send')) {
            return;
        }

        $data['notice'] = $notice;
        $class = new $namespace($data);
        $class->send();
    }
}
