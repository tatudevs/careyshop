<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    用户组模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2018/3/29
 */

namespace app\common\model;

use think\facade\Cache;

class AuthGroup extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'group_id';

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'group_id',
        'system',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'group_id' => 'integer',
        'system'   => 'integer',
        'sort'     => 'integer',
        'status'   => 'integer',
    ];

    /**
     * hasMany cs_auth_rule
     * @access public
     * @return mixed
     */
    public function hasAuthRule()
    {
        return $this->hasMany(AuthRule::class, 'group_id');
    }

    /**
     * 添加一个用户组
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addAuthGroupItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['group_id'], $data['system']);

        if ($this->save($data)) {
            Cache::tag('CommonAuth')->clear();
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个用户组
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setAuthGroupItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        $map[] = ['group_id', '=', $data['group_id']];

        $result = self::update($data, $map);
        Cache::tag('CommonAuth')->clear();

        return $result->toArray();
    }

    /**
     * 获取一个用户组
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAuthGroupItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $result = $this->find($data['group_id']);
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 删除一个用户组
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function delAuthGroupItem(array $data)
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        $result = $this->with('has_auth_rule')->find($data['group_id']);
        if (is_null($result)) {
            return $this->setError('数据不存在');
        }

        if ($result->getAttr('system') === 1) {
            return $this->setError('系统保留用户组不允许删除');
        }

        // 查询是否已被使用
        if (User::checkUnique(['group_id' => $data['group_id']])) {
            return $this->setError('当前用户组已被顾客组账号使用');
        }

        if (Admin::checkUnique(['group_id' => $data['group_id']])) {
            return $this->setError('当前用户组已被管理组账号使用');
        }

        // 删除本身与规则表中的数据
        $result->together(['has_auth_rule'])->delete();
        Cache::tag('CommonAuth')->clear();

        return true;
    }

    /**
     * 获取用户组列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAuthGroupList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        !isset($data['exclude_id']) ?: $map[] = ['group_id', 'not in', $data['exclude_id']];
        is_empty_parm($data['module']) ?: $map[] = ['module', '=', $data['module']];
        is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];

        return $this->setDefaultOrder(['group_id' => 'asc'], ['sort' => 'asc'])
            ->cache(true, null, 'CommonAuth')
            ->where($map)
            ->withSearch(['order'], $data)
            ->select()
            ->toArray();
    }

    /**
     * 批量设置用户组状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setAuthGroupStatus(array $data)
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['group_id', 'in', $data['group_id']];

        self::update(['status' => $data['status']], $map);
        Cache::tag('CommonAuth')->clear();

        return true;
    }

    /**
     * 设置用户组排序
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setAuthGroupSort(array $data)
    {
        if (!$this->validateData($data, 'sort')) {
            return false;
        }

        $map[] = ['group_id', '=', $data['group_id']];

        self::update(['sort' => $data['sort']], $map);
        Cache::tag('CommonAuth')->clear();

        return true;
    }

    /**
     * 根据编号自动排序
     * @access public
     * @param  $data
     * @return bool
     * @throws \Exception
     */
    public function setAuthGroupIndex($data)
    {
        if (!$this->validateData($data, 'index')) {
            return false;
        }

        $list = [];
        foreach ($data['group_id'] as $key => $value) {
            $list[] = ['group_id' => $value, 'sort' => $key + 1];
        }

        $this->saveAll($list);
        Cache::tag('CommonAuth')->clear();

        return true;
    }
}
