<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    问答模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/25
 */

namespace app\common\model;

class Ask extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'ask_id';

    /**
     * 主题
     * @var int
     */
    const ASK_TYPT_THEME = 0;

    /**
     * 提问
     * @var int
     */
    const ASK_TYPT_ASK = 1;

    /**
     * 回答
     * @var int
     */
    const ASK_TYPT_ANSWER = 2;

    /**
     * 是否需要自动写入时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    /**
     * 更新日期字段
     * @var bool/string
     */
    protected $updateTime = false;

    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'parent_id',
        'is_delete',
    ];

    /**
     * hasOne cs_user
     * @access public
     * @return mixed
     */
    public function getUser()
    {
        return $this->hasOne(User::class, 'user_id', 'user_id')->joinType('left');
    }

    /**
     * hasMany cs_ask
     * @access public
     * @return mixed
     */
    public function getItems()
    {
        return $this->hasMany(Ask::class, 'parent_id');
    }

    /**
     * 添加一个提问
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addAskItem($data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 开启事务
        $this->startTrans();

        try {
            $data['user_id'] = get_client_id();
            $data['type'] = self::ASK_TYPT_THEME;

            if (!$this->allowField(['user_id', 'ask_type', 'type', 'title'])->save($data)) {
                throw new \Exception($this->getError());
            }

            $data['type'] = self::ASK_TYPT_ASK;
            $data['parent_id'] = $this->getAttr('ask_id');

            $field = ['user_id', 'parent_id', 'ask_type', 'type', 'ask'];
            Ask::create($data, $field);

            $this->commit();
            return $this->toArray();
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 删除一条记录
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function delAskItem($data)
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        // 搜索条件
        $map[] = ['ask_id', '=', $data['ask_id']];
        is_client_admin() ?: $map[] = ['user_id', '=', get_client_id()];
        $map[] = ['is_delete', '=', 0];

        $result = $this->where($map)->find();
        if (is_null($result)) {
            return true;
        }

        if ($result->getAttr('type') === self::ASK_TYPT_THEME) {
            self::update(['is_delete' => 1], ['ask_id|parent_id' => $data['ask_id']]);
        } else {
            $result->save(['is_delete' => 1]);
        }

        return true;
    }

    /**
     * 在主题上提交一个提问或回答
     * @access private
     * @param array $data 提交数据
     * @param bool  $isQa true:咨询 false:回复
     * @return false|array
     * @throws
     */
    private function addAskOrAnswer($data, $isQa)
    {
        // 搜索条件
        $map[] = ['parent_id', '=', 0];
        $map[] = ['ask_id', '=', $data['ask_id']];
        !$isQa ?: $map[] = ['user_id', '=', get_client_id()];
        $map[] = ['is_delete', '=', 0];

        $result = $this->where($map)->find();
        if (is_null($result)) {
            return $this->setError('数据不存在');
        }

        // 开启事务
        $this->startTrans();

        try {
            // 更新主题
            $result->save(['status' => (int)!$isQa]);

            // 准备回复的内容
            $newData = $result->toArray();
            $newData['parent_id'] = $newData['ask_id'];
            $newData['type'] = $isQa ? self::ASK_TYPT_ASK : self::ASK_TYPT_ANSWER;
            $isQa ? $newData['ask'] = $data['ask'] : $newData['answer'] = $data['answer'];

            // 避免无关数据
            unset($newData['ask_id']);
            unset($newData['title']);
            unset($newData['status']);
            unset($newData['create_time']);

            $newDB = Ask::create($newData);
            $this->commit();

            return $newDB->toArray();
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 回答一个提问
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function replyAskItem($data)
    {
        if (!$this->validateData($data, 'reply')) {
            return false;
        }

        $result = $this->addAskOrAnswer($data, false);
        if (false !== $result) {
            return $result;
        }

        return false;
    }

    /**
     * 在提问上继续提问
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function continueAskItem($data)
    {
        if (!$this->validateData($data, 'continue')) {
            return false;
        }

        $result = $this->addAskOrAnswer($data, true);
        if (false !== $result) {
            return $result;
        }

        return false;
    }

    /**
     * 获取一个问答明细
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAskItem($data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        // 搜索条件
        $map[] = ['ask.ask_id', '=', $data['ask_id']];
        $map[] = ['ask.is_delete', '=', 0];
        is_client_admin() ?: $map[] = ['ask.user_id', '=', get_client_id()];

        // 关联数据
        $withUser['getUser'] = ['username', 'nickname', 'level_icon', 'head_pic'];
        $withItem['get_items'] = function ($query) {
            $query->withoutField('user_id,ask_type,title,status')->order('ask_id');
        };

        // 获取主题与账号信息
        $result = self::withoutGlobalScope()
            ->withoutField('ask,answer')
            ->withJoin($withUser)
            ->with($withItem)
            ->where($map)
            ->find();

        if (!is_null($result)) {
            $temp = [$result->toArray()];
            self::keyToSnake(['getUser'], $temp);

            return $temp[0];
        }

        return null;
    }

    /**
     * 获取问答主题列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAskList($data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map[] = ['ask.parent_id', '=', 0];
        $map[] = ['ask.type', '=', self::ASK_TYPT_THEME];
        $map[] = ['ask.is_delete', '=', 0];
        is_empty_parm($data['ask_type']) ?: $map[] = ['ask.ask_type', '=', $data['ask_type']];
        is_empty_parm($data['status']) ?: $map[] = ['ask.status', '=', $data['status']];

        // 关联查询
        $with = [];

        // 后台管理搜索
        if (is_client_admin()) {
            $with['getUser'] = ['username', 'nickname', 'level_icon', 'head_pic'];
            empty($data['account']) ?: $map[] = ['getUser.username|getUser.nickname', '=', $data['account']];
        } else {
            $map[] = ['ask.user_id', '=', get_client_id()];
        }

        $result['total_result'] = $this->withJoin($with, 'inner')->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['ask_id' => 'desc'])
            ->withoutField('ask,answer')
            ->withJoin($with, 'inner')
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        self::keyToSnake(['getUser'], $result['items']);
        return $result;
    }
}
