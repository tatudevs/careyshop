<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    规则模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/28
 */

namespace app\careyshop\model;

use think\facade\Cache;

class AuthRule extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'rule_id';

    /**
     * 只读属性
     * @var string[]
     */
    protected $readonly = [
        'rule_id',
        'group_id',
        'module',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'rule_id'   => 'integer',
        'group_id'  => 'integer',
        'menu_auth' => 'array',
        'log_auth'  => 'array',
        'sort'      => 'integer',
        'status'    => 'integer',
    ];

    /**
     * 添加一条规则
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addAuthRuleItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['rule_id']);
        !empty($data['menu_auth']) ?: $data['menu_auth'] = [];
        !empty($data['log_auth']) ?: $data['log_auth'] = [];

        $map[] = ['module', '=', $data['module']];
        $map[] = ['group_id', '=', $data['group_id']];

        if (self::checkUnique($map)) {
            return $this->setError('当前模块下已存在相同用户组');
        }

        if ($this->save($data)) {
            Cache::tag('CommonAuth')->clear();
            return $this->toArray();
        }

        return false;
    }

    /**
     * 获取一条规则
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getAuthRuleItem(array $data)
    {
        if (!$this->validateData($data, 'get')) {
            return false;
        }

        return $this->findOrEmpty($data['rule_id'])->toArray();
    }

    /**
     * 编辑一条规则
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setAuthRuleItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        // 数组字段特殊处理
        if (isset($data['menu_auth']) && '' == $data['menu_auth']) {
            $data['menu_auth'] = [];
        }

        if (isset($data['log_auth']) && '' == $data['log_auth']) {
            $data['log_auth'] = [];
        }

        // 获取原始数据
        $result = $this->find($data['rule_id']);
        if (is_null($result)) {
            return $this->setError('数据不存在');
        }

        if (!empty($data['module'])) {
            $map[] = ['rule_id', '<>', $data['rule_id']];
            $map[] = ['module', '=', $data['module']];
            $map[] = ['group_id', '=', $result->getAttr('group_id')];

            if (self::checkUnique($map)) {
                return $this->setError('当前模块下已存在相同用户组');
            }
        }


        if ($result->save($data)) {
            Cache::tag('CommonAuth')->clear();
            return $result->toArray();
        }

        return false;
    }

    /**
     * 批量删除规则
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delAuthRuleList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        self::destroy($data['rule_id']);
        Cache::tag('CommonAuth')->clear();

        return true;
    }

    /**
     * 获取规则列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAuthRuleList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['group_id']) ?: $map[] = ['group_id', '=', $data['group_id']];
        is_empty_parm($data['module']) ?: $map[] = ['module', '=', $data['module']];
        is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];

        return $this->setDefaultOrder(['rule_id' => 'asc'], ['sort' => 'asc'], true)
            ->cache(true, null, 'CommonAuth')
            ->where($map)
            ->withSearch(['order'], $data)
            ->select()
            ->toArray();
    }

    /**
     * 批量设置规则状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setAuthRuleStatus(array $data): bool
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['rule_id', 'in', $data['rule_id']];

        self::update(['status' => $data['status']], $map);
        Cache::tag('CommonAuth')->clear();

        return true;
    }

    /**
     * 设置规则排序
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setAuthRuleSort(array $data): bool
    {
        if (!$this->validateData($data, 'sort')) {
            return false;
        }

        $map[] = ['rule_id', '=', $data['rule_id']];

        self::update(['sort' => $data['sort']], $map);
        Cache::tag('CommonAuth')->clear();

        return true;
    }

    /**
     * 根据编号自动排序
     * @access public
     * @param array $data
     * @return bool
     */
    public function setAuthRuleIndex(array $data): bool
    {
        if (!$this->validateData($data, 'index')) {
            return false;
        }

        foreach ($data['rule_id'] as $key => $value) {
            self::update(['sort' => $key + 1], ['rule_id' => $value]);
        }

        Cache::tag('CommonAuth')->clear();
        return true;
    }

    /**
     * 根据用户组编号与对应模块获取权限明细
     * @access public
     * @param string $module  对应模块
     * @param int    $groupId 用户组编号
     * @return array
     * @throws
     */
    public static function getMenuAuthRule(string $module, int $groupId): array
    {
        // 需要加入游客组的权限(已登录账号也可以使用游客权限)
        if (AUTH_GUEST !== $groupId) {
            $groupId = [$groupId, AUTH_GUEST];
        }

        $map[] = ['module', '=', $module];
        $map[] = ['group_id', 'in', is_array($groupId) ? $groupId : [$groupId]];
        $map[] = ['status', '=', 1];

        $menuAuth = [];
        $logAuth = [];
        $whiteList = [];

        $result = self::where($map)->cache(true, null, 'CommonAuth')->select()->toArray();
        foreach ($result as $value) {
            // 默认将所有获取到的编号都归入数组
            if (!empty($value['menu_auth'])) {
                $menuAuth = [...$menuAuth, ...$value['menu_auth']];

                // 游客组需要将权限加入白名单列表
                if (AUTH_GUEST == $value['group_id']) {
                    $whiteList = [...$whiteList, ...$value['menu_auth']];
                }
            }

            if (!empty($value['log_auth'])) {
                $logAuth = [...$logAuth, ...$value['log_auth']];
            }
        }

        return [
            'menu_auth'  => array_unique($menuAuth),
            'log_auth'   => array_unique($logAuth),
            'white_list' => $whiteList,
        ];
    }
}
