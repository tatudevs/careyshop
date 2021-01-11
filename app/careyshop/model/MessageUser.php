<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    用户消息通知模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/27
 */

namespace app\careyshop\model;

use app\careyshop\validate\Message as Validate;

class MessageUser extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'message_user_id';

    /**
     * 是否需要自动写入时间戳
     * @var bool|string
     */
    protected $autoWriteTimestamp = true;

    /**
     * 更新日期字段
     * @var bool|string
     */
    protected $updateTime = false;

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'message_user_id',
        'message_id',
        'user_id',
        'admin_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'message_user_id' => 'integer',
        'message_id'      => 'integer',
        'user_id'         => 'integer',
        'admin_id'        => 'integer',
        'is_read'         => 'integer',
        'is_delete'       => 'integer',
    ];

    /**
     * 插入记录或更新单条记录,并设为已读状态
     * @access public
     * @param int $messageId 消息编号
     * @return bool
     * @throws
     */
    public function updateMessageUserItem(int $messageId): bool
    {
        $clientType = is_client_admin() ? 'admin_id' : 'user_id';
        $map[] = ['message_id', '=', $messageId];
        $map[] = [$clientType, '=', get_client_id()];

        $result = $this->where($map)->find();
        if ($result) {
            $result->save(['is_read' => 1]); // 存在则更新为已读
            return true;
        }

        $this->insert([
            'message_id' => $messageId,
            $clientType  => get_client_id(),
            'is_read'    => 1,
        ]);

        return true;
    }

    /**
     * 批量插入记录或更新记录
     * @access public
     * @param array  $messageId 消息编号
     * @param string $field     字段
     * @param bool   $isAll     是否操作所有
     * @return bool
     */
    private function updateMessageUserList(array $messageId, string $field, $isAll = false): bool
    {
        // 获取已存在的消息
        $clientType = is_client_admin() ? 'admin_id' : 'user_id';
        $map[] = ['message_id', 'in', $messageId];
        $map[] = [$clientType, '=', get_client_id()];
        $unreadList = $this->where($map)->column('message_id');

        // 补齐不存在记录
        $notExistsId = array_diff($messageId, $unreadList);
        if (!empty($notExistsId)) {
            $dataUser = null;
            foreach ($notExistsId as $item) {
                $dataUser[] = [
                    'message_id'  => $item,
                    $clientType   => get_client_id(),
                    $field        => 1,
                    'create_time' => time(),
                ];
            }

            if ($dataUser) {
                $this->insertAll($dataUser);
            }
        }

        // 更新已存在记录
        if (true === $isAll) {
            $mapAll[] = [$clientType, '=', get_client_id()];
            self::update([$field => 1], $mapAll);
        } else {
            $existsId = array_intersect($messageId, $unreadList);
            if (!empty($existsId)) {
                $map[] = ['message_id', 'in', $existsId];
                self::update([$field => 1], $map);
            }
        }

        return true;
    }

    /**
     * 用户批量设置消息已读
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setMessageUserRead(array $data): bool
    {
        if (!$this->validateData($data, 'user', false, Validate::class)) {
            return false;
        }

        $map[] = ['message_id', 'in', $data['message_id']];
        !isset($data['type']) ?: $map[] = ['type', '=', $data['type']];
        $map[] = ['member', '=', is_client_admin() ? 2 : 1];
        $map[] = ['status', '=', 1];
        $map[] = ['is_delete', '=', 0];

        $messageId = Message::where($map)->column('message_id');
        if (empty($messageId)) {
            return true;
        }

        return $this->updateMessageUserList($messageId, 'is_read');
    }

    /**
     * 用户设置消息全部已读
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setMessageUserAllRead(array $data): bool
    {
        if (!$this->validateData($data, 'unread', false, Validate::class)) {
            return false;
        }

        !isset($data['type']) ?: $map[] = ['type', '=', $data['type']];
        $map[] = ['member', '=', is_client_admin() ? 2 : 1];
        $map[] = ['status', '=', 1];
        $map[] = ['is_delete', '=', 0];

        $messageId = Message::where($map)->column('message_id');
        if (empty($messageId)) {
            return true;
        }

        return $this->updateMessageUserList($messageId, 'is_read', true);
    }

    /**
     * 用户批量删除消息
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delMessageUserList(array $data): bool
    {
        if (!$this->validateData($data, 'user', false, Validate::class)) {
            return false;
        }

        $map[] = ['message_id', 'in', $data['message_id']];
        !isset($data['type']) ?: $map[] = ['type', '=', $data['type']];
        $map[] = ['member', '=', is_client_admin() ? 2 : 1];
        $map[] = ['status', '=', 1];
        $map[] = ['is_delete', '=', 0];

        $messageId = Message::where($map)->column('message_id');
        if (empty($messageId)) {
            return true;
        }

        return $this->updateMessageUserList($messageId, 'is_delete');
    }

    /**
     * 用户删除全部消息
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delMessageUserAll(array $data): bool
    {
        if (!$this->validateData($data, 'unread', false, Validate::class)) {
            return false;
        }

        !isset($data['type']) ?: $map[] = ['type', '=', $data['type']];
        $map[] = ['member', '=', is_client_admin() ? 2 : 1];
        $map[] = ['status', '=', 1];
        $map[] = ['is_delete', '=', 0];

        $messageId = Message::where($map)->column('message_id');
        if (empty($messageId)) {
            return true;
        }

        return $this->updateMessageUserList($messageId, 'is_delete', true);
    }
}
