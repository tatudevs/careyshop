<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    应用管理模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/23
 */

namespace app\careyshop\model;

use careyshop\facade\Captcha;
use think\facade\Cache;

class App extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'app_id';

    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'is_delete',
    ];

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'app_id',
        'app_key',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'app_id'    => 'integer',
        'app_key'   => 'integer',
        'captcha'   => 'integer',
        'status'    => 'integer',
        'is_delete' => 'integer',
    ];

    /**
     * 定义全局的查询范围
     * @var string[]
     */
    protected $globalScope = [
        'delete',
    ];

    /**
     * 全局是否删除查询条件
     * @access public
     * @param object $query 模型
     */
    public function scopeDelete(object $query)
    {
        $query->where('is_delete', '=', 0);
    }

    /**
     * 生成唯一应用Key
     * @access private
     * @return string
     */
    private function getAppKey(): string
    {
        do {
            $appKey = rand_number();
        } while (self::checkUnique(['app_key' => $appKey]));

        return $appKey;
    }

    /**
     * 添加一个应用
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addAppItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 初始化部分数据
        $data['app_key'] = $this->getAppKey();
        $data['app_secret'] = rand_string();
        unset($data['app_id']);

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个应用
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setAppItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        if (!empty($data['app_name'])) {
            $map[] = ['app_id', '<>', $data['app_id']];
            $map[] = ['app_name', '=', $data['app_name']];

            if (self::checkUnique($map)) {
                return $this->setError('应用名称已存在');
            }
        }

        // 允许修改字段与条件
        $field = ['app_name', 'captcha', 'status'];
        $map = [['app_id', '=', $data['app_id']]];

        $result = self::update($data, $map, $field);
        Cache::tag('app')->clear();

        return $result->toArray();
    }

    /**
     * 获取一个应用
     * @access public
     * @param array $data 外部数据
     * @return array|false|null
     * @throws
     */
    public function getAppItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $result = $this->find($data['app_id']);
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 获取应用列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAppList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];
        empty($data['app_name']) ?: $map[] = ['app_name', 'like', '%' . $data['app_name'] . '%'];

        return $this->where($map)->select()->toArray();
    }

    /**
     * 批量删除应用
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delAppList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        // 搜索条件
        $map[] = ['app_id', 'in', $data['app_id']];

        self::update(['is_delete' => 1], $map);
        Cache::tag('app')->clear();

        return true;
    }

    /**
     * 查询应用名称是否已存在
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws \Exception
     */
    public function uniqueAppName(array $data): bool
    {
        if (!$this->validateData($data, 'unique')) {
            return false;
        }

        $map[] = ['app_name', '=', $data['app_name']];
        !isset($data['exclude_id']) ?: $map[] = ['app_id', '<>', $data['exclude_id']];

        if (self::checkUnique($map)) {
            return $this->setError('应用名称已存在');
        }

        return true;
    }

    /**
     * 更换应用Secret
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function replaceAppSecret(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $map[] = ['app_id', '=', $data['app_id']];
        $result = self::update(['app_secret' => rand_string()], $map);
        Cache::tag('app')->clear();

        return $result->toArray();
    }

    /**
     * 批量设置应用验证码
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setAppCaptcha(array $data): bool
    {
        if (!$this->validateData($data, 'captcha')) {
            return false;
        }

        $map[] = ['app_id', 'in', $data['app_id']];
        self::update(['captcha' => $data['captcha']], $map);
        Cache::tag('app')->clear();

        return true;
    }

    /**
     * 批量设置应用状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setAppStatus(array $data): bool
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['app_id', 'in', $data['app_id']];
        self::update(['status' => $data['status']], $map);
        Cache::tag('app')->clear();

        return true;
    }

    /**
     * 查询应用验证码状态
     * @access public
     * @param string $key    外部数据
     * @param bool   $is_key 是否创建标识
     * @return array
     * @throws
     */
    public static function getAppCaptcha(string $key, $is_key = true): array
    {
        $result = [
            'captcha'    => true,
            'session_id' => '',
        ];

        if (empty($key)) {
            return $result;
        }

        $appResult = self::where('app_key', '=', $key)->find();
        if (!is_null($appResult)) {
            if ($appResult->getAttr('captcha') === 0) {
                $result['captcha'] = false;
                return $result;
            }
        }

        if ($is_key) {
            if (-1 === get_client_type()) {
                $result['session_id'] = Captcha::getKeyId(rand_string());
            } else {
                $result['session_id'] = Captcha::getKeyId(get_client_token());
            }
        }

        return $result;
    }
}
