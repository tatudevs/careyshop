<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    消息模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/27
 */

namespace app\careyshop\model;

class Message extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'message_id';

    /**
     * 是否需要自动写入时间戳
     * @var bool|string
     */
    protected $autoWriteTimestamp = true;

    /**
     * 隐藏属性
     * @var mixed|string[]
     */
    protected $hidden = [
        'is_delete',
    ];

    /**
     * 只读属性
     * @var mixed|string[]
     */
    protected $readonly = [
        'message_id',
        'member',
        'create_time',
    ];

    /**
     * 字段类型或者格式转换
     * @var mixed|string[]
     */
    protected $type = [
        'message_id' => 'integer',
        'type'       => 'integer',
        'member'     => 'integer',
        'page_views' => 'integer',
        'is_top'     => 'integer',
        'status'     => 'integer',
        'is_delete'  => 'integer',
    ];

    /**
     * hasOne cs_message_user
     * @access public
     * @return mixed
     */
    public function getMessageUser()
    {
        return $this->hasOne(MessageUser::class, 'message_id');
    }

    /**
     * 添加一条消息
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addMessageItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关参数及初始化部分数据
        unset($data['message_id'], $data['page_views']);

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 添加一条私有函(内部调用)
     * @access public
     * @param array $data       消息结构数据
     * @param array $clientId   账号编号
     * @param int   $clientType 消息成员组 0=顾客组 1=管理组
     * @return bool
     */
    public function inAddMessageItem(array $data, array $clientId, int $clientType): bool
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关参数及初始化部分数据
        $data['member'] = 0;
        unset($data['message_id'], $data['page_views']);

        // 开启事务
        $this->startTrans();

        try {
            // 保存主数据
            $this->save($data);

            $messageUserData = [];
            $clientType = $clientType == 0 ? 'user_id' : 'admin_id';

            foreach ($clientId as $value) {
                $messageUserData[] = [
                    'message_id'  => $this->getAttr('message_id'),
                    $clientType   => $value,
                    'is_read'     => 0,
                    'create_time' => time(),
                ];
            }

            MessageUser::insertAll($messageUserData);
            $this->commit();

            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 编辑一条消息
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setMessageItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        // 搜索条件
        $map[] = ['message_id', '=', $data['message_id']];
        $map[] = ['member', '<>', 0];
        $map[] = ['is_delete', '=', 0];

        $result = $this->where($map)->find();
        if (is_null($result)) {
            return $this->setError('消息不存在');
        }

        if ($result->getAttr('status') === 1) {
            return $this->setError('消息已发布，不允许编辑！');
        }

        if ($result->save($data)) {
            return $result->toArray();
        }

        return false;
    }

    /**
     * 批量删除消息
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delMessageList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        $map[] = ['message_id', 'in', $data['message_id']];
        $map[] = ['member', '<>', 0];
        $map[] = ['is_delete', '=', 0];

        self::update(['is_delete' => 1], $map);
        return true;
    }

    /**
     * 批量正式发布消息
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setMessageStatus(array $data): bool
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['message_id', 'in', $data['message_id']];
        $map[] = ['member', '<>', 0];
        $map[] = ['status', '=', 0];
        $map[] = ['is_delete', '=', 0];

        self::update(['status' => 1], $map);
        return true;
    }

    /**
     * 获取一条消息(后台)
     * @access public
     * @param array $data 外部数据
     * @return array|false|null
     * @throws
     */
    public function getMessageItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $map[] = ['message_id', '=', $data['message_id']];
        $map[] = ['member', '<>', 0];
        $map[] = ['is_delete', '=', 0];

        $result = $this->where($map)->find();
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 用户获取一条消息
     * @access public
     * @param array $data 外部数据
     * @return array|false|null
     * @throws
     */
    public function getMessageUserItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $map[] = ['message_id', '=', $data['message_id']];
        $map[] = ['status', '=', 1];
        $map[] = ['is_delete', '=', 0];

        $result = $this->where($map)->find();
        if (is_null($result)) {
            return null;
        }

        // 验证是否有阅读权限
        $map = [
            ['message_id', '=', $data['message_id']],
            [is_client_admin() ? 'admin_id' : 'user_id', '=', get_client_id()],
        ];

        $userDb = new MessageUser();
        $userResult = $userDb->where($map)->value('is_delete');

        switch ($result->getAttr('member')) {
            case 0:
                $notReadable = $userResult === 1;
                break;
            case 1:
                $notReadable = is_client_admin() || $userResult === 1;
                break;
            case 2:
                $notReadable = !is_client_admin() || $userResult === 1;
                break;
            default:
                $notReadable = true;
        }

        if ($notReadable) {
            return null;
        }

        // 存在权限则需要插入记录与更新
        $result->inc('page_views')->update();
        $userDb->updateMessageUserItem($data['message_id']);

        return $result->toArray();
    }

    /**
     * 获取消息列表(后台)
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getMessageList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $isMember = is_empty_parm($data['member']);
        is_empty_parm($data['type']) ?: $map[] = ['type', '=', $data['type']];
        empty($data['title']) ?: $map[] = ['title', 'like', '%' . $data['title'] . '%'];
        is_empty_parm($data['is_top']) ?: $map[] = ['is_top', '=', $data['is_top']];
        is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];
        $map[] = ['member', !$isMember ? '=' : '<>', !$isMember ? $data['member'] : 0];
        $map[] = ['is_delete', '=', 0];

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['message_id' => 'desc'])
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        return $result;
    }

    /**
     * 用户获取未读消息数
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getMessageUserUnread(array $data)
    {
        if (!$this->validateData($data, 'unread')) {
            return false;
        }

        // 获取用户编号(管理组或顾客组)
        $clientId = get_client_id();

        if (is_client_admin()) {
            $member = '= 2';
            $clientType = 'admin_id';
            $createTime = Admin::where($clientType, '=', $clientId)->value('create_time');
        } else {
            $member = '< 2';
            $clientType = 'user_id';
            $createTime = User::where($clientType, '=', $clientId)->value('create_time');
        }

        is_empty_parm($data['type']) ?: $map[] = ['m.type', '=', $data['type']];
        $map[] = ['m.status', '=', 1];
        $map[] = ['m.is_delete', '=', 0];
        $map[] = ['m.create_time', '>=', $createTime];

        // 构建子语句
        $userSQL = MessageUser::where($clientType, '=', $clientId)->buildSql();

        // 联合查询语句
        $userWhere_1 = '`u`.' . $clientType . ' IS NULL OR `u`.' . $clientType . ' = :' . $clientType;
        $userWhere_2 = '`u`.' . $clientType . ' IS NULL OR `u`.is_delete = 0';
        $userWhere_3 = '`u`.' . $clientType . ' IS NOT NULL OR `m`.member ' . $member;

        $result = $this
            ->alias('m')
            ->field('`m`.`type`, COUNT(*) AS total')
            ->join([$userSQL => 'u'], 'u.message_id = m.message_id', 'left')
            ->where($userWhere_1, [$clientType => [$clientId, \PDO::PARAM_INT]])
            ->where($userWhere_2)
            ->where($userWhere_3)
            ->where('`u`.' . $clientType . ' IS NULL OR `u`.is_read = 0')
            ->where($map)
            ->group('`m`.`type`')
            ->select();

        $total = $result->column('total', 'type');
        $total['total'] = array_sum($total);

        return $total;
    }

    /**
     * 用户获取消息列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getMessageUserList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 获取用户编号(管理组或顾客组)
        $clientId = get_client_id();

        if (is_client_admin()) {
            $member = '= 2';
            $clientType = 'admin_id';
            $createTime = Admin::where($clientType, '=', $clientId)->value('create_time');
        } else {
            $member = '< 2';
            $clientType = 'user_id';
            $createTime = User::where($clientType, '=', $clientId)->value('create_time');
        }

        is_empty_parm($data['type']) ?: $map[] = ['m.type', '=', $data['type']];
        $map[] = ['m.status', '=', 1];
        $map[] = ['m.is_delete', '=', 0];
        $map[] = ['m.create_time', '>=', $createTime];
        $mapRead = null;

        // 是否已读需要特殊对待
        if (!is_empty_parm($data['is_read'])) {
            switch ($data['is_read']) {
                case 0:
                    $mapRead = '`u`.' . $clientType . ' IS NULL OR `u`.is_read = 0';
                    break;

                case 1:
                    $mapRead[] = ['u.is_read', '=', 1];
                    break;
            }
        }

        // 构建子语句
        $userSQL = MessageUser::where($clientType, '=', $clientId)->buildSql();

        // 联合查询语句
        $userWhere_1 = '`u`.' . $clientType . ' IS NULL OR `u`.' . $clientType . ' = :' . $clientType;
        $userWhere_2 = '`u`.' . $clientType . ' IS NULL OR `u`.is_delete = 0';
        $userWhere_3 = '`u`.' . $clientType . ' IS NOT NULL OR `m`.member ' . $member;

        $totalResult = $this
            ->alias('m')
            ->join([$userSQL => 'u'], 'u.message_id = m.message_id', 'left')
            ->where($userWhere_1, [$clientType => [$clientId, \PDO::PARAM_INT]])
            ->where($userWhere_2)
            ->where($userWhere_3)
            ->where($mapRead)
            ->where($map)
            ->count();

        $message = ['total_result' => $totalResult];
        if (!empty($data['is_unread']) && empty($data['is_read'])) {
            $unread = $this->getMessageUserUnread([]);
            if (false !== $unread) {
                $message['unread_count'] = $unread;
            }
        }

        if ($totalResult <= 0) {
            return $message;
        }

        // 实际查询
        $result = $this->setDefaultOrder(['message_id' => 'desc'], ['is_top' => 'desc'], true)
            ->alias('m')
            ->field('m.message_id,m.type,m.title,m.url,m.is_top,m.target,ifnull(`u`.is_read, 0) is_read,m.create_time')
            ->join([$userSQL => 'u'], 'u.message_id = m.message_id', 'left')
            ->where($userWhere_1, [$clientType => [$clientId, \PDO::PARAM_INT]])
            ->where($userWhere_2)
            ->where($userWhere_3)
            ->where($mapRead)
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select();

        $message['items'] = $result->toArray();
        return $message;
    }
}
