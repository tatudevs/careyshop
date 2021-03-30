<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    菜单管理模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/26
 */

namespace app\careyshop\model;

use think\facade\Cache;

class Menu extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'menu_id';

    /**
     * 菜单权限
     * @var array
     */
    private static array $menuAuth = [];

    /**
     * 只读属性
     * @var mixed|string[]
     */
    protected $readonly = [
        'menu_id',
        'module',
    ];

    /**
     * 字段类型或者格式转换
     * @var mixed|string[]
     */
    protected $type = [
        'menu_id'   => 'integer',
        'parent_id' => 'integer',
        'type'      => 'integer',
        'is_navi'   => 'integer',
        'sort'      => 'integer',
        'status'    => 'integer',
    ];

    /**
     * URL驼峰转下划线修改器
     * @access protected
     * @param string $value 值
     * @return string
     */
    private function strToSnake(string $value): string
    {
        if (empty($value) || !is_string($value)) {
            return $value;
        }

        $word = explode('/', $value);
        $word = array_map(['think\\helper\\Str', 'snake'], $word);

        return implode('/', $word);
    }

    /**
     * 添加一个菜单
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addMenuItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段,并且转换格式
        unset($data['menu_id']);
        empty($data['url']) ?: $data['url'] = $this->strToSnake($data['url']);

        if (!empty($data['url']) && 0 == $data['type']) {
            $map[] = ['module', '=', $data['module']];
            $map[] = ['type', '=', 0];
            $map[] = ['url', '=', $data['url']];

            if (self::checkUnique($map)) {
                return $this->setError('Url已存在');
            }
        }

        if ($this->save($data)) {
            Cache::tag('CommonAuth')->clear();
            return $this->toArray();
        }

        return false;
    }

    /**
     * 获取一个菜单
     * @access public
     * @param array $data 外部数据
     * @return array|false|null
     * @throws
     */
    public function getMenuItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $result = $this->find($data['menu_id']);
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 编辑一个菜单
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setMenuItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        $result = $this->find($data['menu_id']);
        if (is_null($result)) {
            return $this->setError('数据不存在');
        }

        // 检测编辑后是否存在重复URL
        empty($data['url']) ?: $data['url'] = $this->strToSnake($data['url']);
        isset($data['type']) ?: $data['type'] = $result->getAttr('type');
        isset($data['url']) ?: $data['url'] = $result->getAttr('url');

        if (!empty($data['url']) && 0 == $data['type']) {
            $map[] = ['menu_id', '<>', $data['menu_id']];
            $map[] = ['module', '=', $result->getAttr('module')];
            $map[] = ['type', '=', 0];
            $map[] = ['url', '=', $data['url']];

            if (self::checkUnique($map)) {
                return $this->setError('Url已存在');
            }
        }

        // 父菜单不能设置成自身或所属的子菜单
        if (isset($data['parent_id'])) {
            if ($data['parent_id'] == $data['menu_id']) {
                return $this->setError('上级菜单不能设为自身');
            }

            $menuList = self::getMenuListData($result->getAttr('module'), $data['menu_id']);
            foreach ($menuList as $value) {
                if ($data['parent_id'] == $value['menu_id']) {
                    return $this->setError('上级菜单不能设为自身的子菜单');
                }
            }
        }

        if ($result->save($data)) {
            Cache::tag('CommonAuth')->clear();
            return $result->toArray();
        }

        return false;
    }

    /**
     * 根据条件获取菜单列表数据
     * @access public static
     * @param string $module  所属模块
     * @param int    $menuId  菜单Id
     * @param bool   $isLayer 是否返回本级菜单
     * @param null   $level   菜单深度
     * @param null   $filter  过滤'is_navi'与'status'
     * @return array
     */
    public static function getMenuListData(string $module, $menuId = 0, $isLayer = false, $level = null, $filter = null): array
    {
        // 缓存名称
        $treeCache = 'MenuTree:' . $module;

        // 搜索条件
        $joinMap = '';
        $map[] = ['m.module', '=', $module];

        // 过滤'is_navi'与'status'
        foreach ((array)$filter as $key => $value) {
            if ($key != 'is_navi' && $key != 'status') {
                continue;
            }

            $map[] = ['m.' . $key, '=', (int)$value];
            $joinMap .= sprintf(' AND s.%s = %d', $key, $value);
            $treeCache .= $key . $value;
        }

        $result = self::cache(true, null, 'CommonAuth')
            ->alias('m')
            ->field('m.*,count(s.menu_id) children_total')
            ->join('menu s', 's.parent_id = m.menu_id' . $joinMap, 'left')
            ->where($map)
            ->group('m.menu_id')
            ->order('m.parent_id,m.sort,m.menu_id')
            ->select();

        $treeCache .= sprintf('id%dlevel%dis_layer%d', $menuId, is_null($level) ? -1 : $level, $isLayer);
        empty(self::$menuAuth) ?: $treeCache .= 'auth' . implode(',', self::$menuAuth);

        if (Cache::has($treeCache)) {
            return Cache::get($treeCache);
        }

        $tree = self::setMenuTree((int)$menuId, $result, $level, $isLayer);
        if (!empty(self::$menuAuth)) {
            foreach ($result as $value) {
                $value->setAttr('level', 0);
                $tree[] = $value;
            }
        }

        Cache::tag('CommonAuth')->set($treeCache, $tree);
        return $tree;
    }

    /**
     * 过滤和排序所有菜单
     * @access private
     * @param int    $parentId   上级菜单Id
     * @param object $list       原始模型对象
     * @param null   $limitLevel 显示多少级深度 null:全部
     * @param bool   $isLayer    是否返回本级菜单
     * @param int    $level      层级深度
     * @return array
     */
    private static function setMenuTree(int $parentId, object &$list, $limitLevel = null, $isLayer = false, $level = 0): array
    {
        static $tree = [];
        $parentId != 0 ?: $isLayer = false; // 返回全部菜单不需要本级

        foreach ($list as $key => $value) {
            // 获取菜单主Id
            $menuId = $value->getAttr('menu_id');

            // 优先处理:存在权限列表则需要检测,否则删除节点
            if (!empty(self::$menuAuth) && !in_array($menuId, self::$menuAuth)) {
                unset($list[$key]);
                continue;
            }

            // 判断菜单是否存在继承关系
            if ($value->getAttr('parent_id') !== $parentId && $menuId !== $parentId) {
                continue;
            }

            // 是否返回本级菜单
            if ($menuId === $parentId && !$isLayer) {
                continue;
            }

            // 限制菜单显示深度
            if (!is_null($limitLevel) && $level > $limitLevel) {
                break;
            }

            $value->setAttr('level', $level);
            $tree[] = $value->toArray();

            // 需要返回本级菜单时保留列表数据,否则引起树的重复,并且需要自增层级
            if (true == $isLayer) {
                $isLayer = false;
                $level++;
                continue;
            }

            // 删除已使用数据,减少查询次数
            unset($list[$key]);

            if ($value->getAttr('children_total') > 0) {
                self::setMenuTree($menuId, $list, $limitLevel, $isLayer, $level + 1);
            }
        }

        return $tree;
    }

    /**
     * 删除一个菜单(影响下级子菜单)
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function delMenuItem(array $data)
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        $result = $this->find($data['menu_id']);
        if (is_null($result)) {
            return $this->setError('数据不存在');
        }

        $menuList = self::getMenuListData($result->getAttr('module'), $data['menu_id'], true);
        $delList = array_column($menuList, 'menu_id');

        self::destroy($delList);
        Cache::tag('CommonAuth')->clear();

        return ['children' => $delList];
    }

    /**
     * 根据Id获取导航数据
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getMenuIdNavi(array $data)
    {
        if (!$this->validateData($data, 'navi')) {
            return false;
        }

        $isLayer = !is_empty_parm($data['is_layer']) ? (bool)$data['is_layer'] : true;
        $filter['is_navi'] = 1;
        $filter['status'] = 1;
        $data['menu_id'] = isset($data['menu_id']) ?: 0;

        return self::getParentList(app('http')->getName(), $data['menu_id'], $isLayer, $filter);
    }

    /**
     * 根据Url获取导航数据
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getMenuUrlNavi(array $data)
    {
        if (!$this->validateData($data, 'url')) {
            return false;
        }

        $isLayer = !is_empty_parm($data['is_layer']) ? (bool)$data['is_layer'] : true;
        $filter['is_navi'] = 1;
        $filter['status'] = 1;
        $filter['url'] = isset($data['url']) ? $data['url'] : null;

        return self::getParentList(app('http')->getName(), 0, $isLayer, $filter);
    }

    /**
     * 批量设置是否导航
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setMenuNavi(array $data): bool
    {
        if (!$this->validateData($data, 'nac')) {
            return false;
        }

        $map[] = ['menu_id', 'in', $data['menu_id']];
        self::update(['is_navi' => $data['is_navi']], $map);
        Cache::tag('CommonAuth')->clear();

        return true;
    }

    /**
     * 设置菜单排序
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setMenuSort(array $data): bool
    {
        if (!$this->validateData($data, 'sort')) {
            return false;
        }

        $map[] = ['menu_id', '=', $data['menu_id']];
        self::update(['sort' => $data['sort']], $map);
        Cache::tag('CommonAuth')->clear();

        return true;
    }

    /**
     * 根据编号自动排序
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setMenuIndex(array $data): bool
    {
        if (!$this->validateData($data, 'index')) {
            return false;
        }

        foreach ($data['menu_id'] as $key => $value) {
            self::update(['sort' => $key + 1], ['menu_id' => $value]);
        }

        Cache::tag('CommonAuth')->clear();
        return true;
    }

    /**
     * 根据编号获取上级菜单列表
     * @access public
     * @param string $module  所属模块
     * @param int    $menuId  菜单编号
     * @param bool   $isLayer 是否返回本级
     * @param null   $filter  过滤'is_navi'与'status'
     * @return array
     */
    public static function getParentList(string $module, int $menuId, $isLayer = false, $filter = null): array
    {
        // 搜索条件
        $map[] = ['module', '=', $module];

        // 过滤'is_navi'与'status'
        foreach ((array)$filter as $key => $value) {
            if ($key != 'is_navi' && $key != 'status') {
                continue;
            }

            $map[] = [$key, '=', $value];
        }

        $list = self::cache(true, null, 'CommonAuth')->where($map)->column('*', 'menu_id');
        // 判断是否根据url获取
        if (isset($filter['url'])) {
            $url = array_column($list, 'menu_id', 'url');
            if (isset($url[$filter['url']])) {
                $menuId = $url[$filter['url']];
                unset($url);
            }
        }

        // 是否返回本级
        if (!$isLayer && isset($list[$menuId])) {
            $menuId = $list[$menuId]['parent_id'];
        }

        $result = [];
        while (true) {
            if (!isset($list[$menuId])) {
                break;
            }

            $result[] = $list[$menuId];

            if ($list[$menuId]['parent_id'] <= 0) {
                break;
            }

            $menuId = $list[$menuId]['parent_id'];
        }

        return array_reverse($result);
    }

    /**
     * 设置菜单状态(影响上下级菜单)
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setMenuStatus(array $data)
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $result = $this->find($data['menu_id']);
        if (is_null($result)) {
            return $this->setError('数据不存在');
        }

        if ($result->getAttr('status') == $data['status']) {
            return $this->setError('状态未改变');
        }

        // 获取当前菜单模块名
        $module = $result->getAttr('module');

        // 如果是启用,则父菜单也需要启用
        $parent = [];
        if ($data['status'] == 1) {
            $parent = self::getParentList($module, $data['menu_id']);
        }

        // 子菜单则无条件继承
        $children = self::getMenuListData($module, $data['menu_id'], true);

        $parent = array_column($parent, 'menu_id');
        $children = array_column($children, 'menu_id');

        $map[] = ['menu_id', 'in', array_merge($parent, $children)];
        $map[] = ['status', '=', $result->getAttr('status')];

        self::update(['status' => $data['status']], $map);
        Cache::tag('CommonAuth')->clear();

        return ['parent' => $parent, 'children' => $children, 'status' => (int)$data['status']];
    }

    /**
     * 获取菜单列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getMenuList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        $menuId = isset($data['menu_id']) ? $data['menu_id'] : 0;
        $isLayer = !is_empty_parm($data['is_layer']) ? (bool)$data['is_layer'] : true;
        $level = isset($data['level']) ? $data['level'] : null;

        $filter = null;
        is_empty_parm($data['is_navi']) ?: $filter['is_navi'] = $data['is_navi'];
        is_empty_parm($data['status']) ?: $filter['status'] = $data['status'];

        return self::getMenuListData($data['module'], $menuId, $isLayer, $level, $filter);
    }

    /**
     * 根据权限获取菜单列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getMenuAuthList(array $data)
    {
        if (!$this->validateData($data, 'auth')) {
            return false;
        }

        // 获取当前登录账号对应的权限数据
        $ruleResult = AuthRule::getMenuAuthRule($data['module'], get_client_group());
        if (empty($ruleResult['menu_auth'])) {
            return [];
        }

        // 当规则表中存在菜单权限时进行赋值,让获取的函数进行过滤
        self::$menuAuth = $ruleResult['menu_auth'];
        $menuId = isset($data['menu_id']) ? $data['menu_id'] : 0;
        $result = self::getMenuListData($data['module'], $menuId, true);
        self::$menuAuth = [];

        return $result;
    }

    /**
     * 获取以URL为索引的菜单列表
     * @access public
     * @param string $module 所属模块
     * @param int    $status 菜单状态
     * @return array
     */
    public static function getUrlMenuList(string $module, $status = 1): array
    {
        // 缓存名称
        $key = 'urlMenuList' . $module . $status;

        $map[] = ['module', '=', $module];
        $map[] = ['status', '=', $status];

        return self::cache($key, null, 'CommonAuth')->where($map)->column('*', 'url');
    }
}
