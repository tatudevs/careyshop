<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    专题模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/10
 */

namespace app\common\model;

class Topic extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'topic_id';

    /**
     * 是否需要自动写入时间戳
     * @var bool|string
     */
    protected $autoWriteTimestamp = true;

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'topic_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'topic_id'    => 'integer',
        'status'      => 'integer',
    ];

    /**
     * 添加一个专题
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addTopicItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['topic_id']);

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个专题
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setTopicItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        $map[] = ['topic_id', '=', $data['topic_id']];
        $result = self::update($data, $map);

        return $result->toArray();
    }

    /**
     * 批量删除专题
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delTopicList(array $data)
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        self::destroy($data['topic_id']);
        return true;
    }

    /**
     * 获取一个专题
     * @access public
     * @param array $data 外部数据
     * @return array|false|null
     * @throws
     */
    public function getTopicItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $map[] = ['topic_id', '=', $data['topic_id']];
        is_client_admin() ?: $map[] = ['status', '=', 1];

        $result = $this->where($map)->find();
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 获取专题列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getTopicList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];

        // 后台管理搜索
        if (is_client_admin()) {
            is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];
            empty($data['title']) ?: $map[] = ['title', 'like', '%' . $data['title'] . '%'];
            empty($data['alias']) ?: $map[] = ['alias', 'like', '%' . $data['alias'] . '%'];
            empty($data['keywords']) ?: $map[] = ['keywords', 'like', '%' . $data['keywords'] . '%'];
        } else {
            $map[] = ['status', '=', 1];
        }

        // 获取总数量,为空直接返回
        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['topic_id' => 'desc'])
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->withoutField('content')
            ->select()
            ->toArray();

        return $result;
    }

    /**
     * 批量设置专题是否显示
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setTopicStatus(array $data)
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['topic_id', 'in', $data['topic_id']];
        self::update(['status' => $data['status']], $map);

        return true;
    }
}
