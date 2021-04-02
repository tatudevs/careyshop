<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    账号等级模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/3
 */

namespace app\careyshop\model;

class UserLevel extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'user_level_id';

    /**
     * 只读属性
     * @var mixed|string[]
     */
    protected $readonly = [
        'user_level_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var mixed|string[]
     */
    protected $type = [
        'user_level_id' => 'integer',
        'amount'        => 'float',
        'discount'      => 'integer',
    ];

    /**
     * 获取一个账号等级
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getLevelItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        return $this->findOrEmpty($data['user_level_id'])->toArray();
    }

    /**
     * 获取账号等级列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getLevelList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        return $this->setDefaultOrder(['amount' => 'asc'])
            ->withSearch(['order'], $data)
            ->select()
            ->toArray();
    }

    /**
     * 添加一个账号等级
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function addLevelItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['user_level_id']);

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个账号等级
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setLevelItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        $map[] = ['user_level_id', '=', $data['user_level_id']];
        $result = $this->where($map)->find();

        if (is_null($result)) {
            return $this->setError('数据不存在');
        }

        // 开启事务
        $this->startTrans();

        try {
            if (!$result->save($data)) {
                // 更新已存在的顾客数据
                User::update(['level_icon' => $result->getAttr('icon')], $map);
            }

            $this->commit();
            return $result->toArray();
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 批量删除账号等级
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delLevelList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        // 搜索条件
        $map[] = ['user_level_id', 'in', $data['user_level_id']];
        if (User::checkUnique($map)) {
            return $this->setError('等级已在使用中,建议进行编辑修改');
        }

        $this->destroy($data['user_level_id']);
        return true;
    }
}
