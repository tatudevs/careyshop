<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    管理组账号模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/9/9
 */

namespace app\careyshop\model;

use careyshop\Ip2Region;
use think\facade\Cache;

class Admin extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'admin_id';

    /**
     * 是否需要自动写入时间戳
     * @var bool|string
     */
    protected $autoWriteTimestamp = true;

    /**
     * 隐藏属性
     * @var string[]
     */
    protected $hidden = [
        'password',
        'is_delete',
    ];

    /**
     * 只读属性
     * @var string[]
     */
    protected $readonly = [
        'admin_id',
        'username',
        'create_time',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'admin_id'   => 'integer',
        'group_id'   => 'integer',
        'last_login' => 'timestamp',
        'status'     => 'integer',
        'is_delete'  => 'integer',
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
     * 密码修改器
     * @access public
     * @param string $value 值
     * @return string
     */
    public function setPasswordAttr(string $value): string
    {
        return user_md5($value);
    }

    /**
     * 获取器最后登录ip
     * @access public
     * @param $value
     * @param $data
     * @return string
     * @throws
     */
    public function getLastIpRegionAttr($value, $data): string
    {
        if (empty($data['last_ip'])) {
            return '';
        }

        $ip2region = new Ip2Region();
        $result = $ip2region->btreeSearch($data['last_ip']);

        return $result ? get_ip2region_str($result['region']) : $value;
    }

    /**
     * hasOne db_token
     * @access public
     * @return object
     */
    public function hasToken(): object
    {
        return $this->hasOne(Token::class, 'admin_id', 'client_id');
    }

    /**
     * hasOne cs_auth_group
     * @access public
     * @return object
     */
    public function getAuthGroup(): object
    {
        return $this
            ->hasOne(AuthGroup::class, 'group_id', 'group_id')
            ->joinType('left')
            ->field('group_id,name,status')
            ->hidden(['group_id']);
    }

    /**
     * 关联查询NULL处理
     * @param null $value
     * @return object
     */
    public function getGetAuthGroupAttr($value = null)
    {
        return $value ?? new \stdClass;
    }

    /**
     * 验证当前账户是否有越级操作
     * @access private
     * @param null $adminID admin_id
     * @param null $data    外部数据
     * @return bool
     * @throws
     */
    private function checkAdminAuth($adminID = null, $data = null): bool
    {
        if (get_client_group() === AUTH_SUPER_ADMINISTRATOR) {
            return true;
        }

        if (!is_null($adminID)) {
            $result = $this->find($adminID);
            if (is_null($result)) {
                return $this->setError('账号不存在');
            }

            if (get_client_group() > $result->getAttr('group_id')) {
                return $this->setError('操作失败，您可能存在越级操作');
            }
        }

        if (!is_empty_parm($data['group_id'])) {
            if (get_client_group() > $data['group_id']) {
                return $this->setError('操作失败，您可能存在越级操作');
            }
        }

        return true;
    }

    /**
     * 添加一个账号
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addAdminItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        if (!$this->checkAdminAuth(null, $data)) {
            return false;
        }

        $field = ['username', 'password', 'group_id', 'nickname', 'head_pic'];
        if ($this->allowField($field)->save($data)) {
            return $this->hidden(['password_confirm'])->toArray();
        }

        return false;
    }

    /**
     * 编辑一个账号
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setAdminItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        // 数据类型修改
        $data['client_id'] = (int)$data['client_id'];
        if (!$this->checkAdminAuth($data['client_id'], $data)) {
            return false;
        }

        if (!empty($data['nickname'])) {
            $nickMap[] = ['admin_id', '<>', $data['client_id']];
            $nickMap[] = ['nickname', '=', $data['nickname']];

            if (self::checkUnique($nickMap)) {
                return $this->setError('昵称已存在');
            }
        }

        if (isset($data['group_id'])) {
            $map[] = ['client_id', '=', $data['client_id']];
            $map[] = ['client_type', '=', 1];

            Cache::tag('token:admin_' . $data['client_id'])->clear();
            $this->hasToken()->where($map)->delete();
        }

        $map = [['admin_id', '=', $data['client_id']]];
        $result = self::update($data, $map, ['group_id', 'nickname', 'head_pic']);

        return $result->toArray();
    }

    /**
     * 批量设置账号状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setAdminStatus(array $data): bool
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        foreach ($data['client_id'] as $item) {
            if (!$this->checkAdminAuth($item)) {
                return false;
            }
        }

        $map = [['admin_id', 'in', $data['client_id']]];
        self::update(['status' => $data['status']], $map);

        foreach ($data['client_id'] as $value) {
            Cache::tag('token:admin_' . $value)->clear();
        }

        $map = [
            ['client_id', 'in', $data['client_id']],
            ['client_type', '=', 1],
        ];

        $this->hasToken()->where($map)->delete();
        return true;
    }

    /**
     * 修改一个账号密码
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function setAdminPassword(array $data): bool
    {
        if (!$this->validateData($data, 'change')) {
            return false;
        }

        if (!$this->checkAdminAuth($data['client_id'], $data)) {
            return false;
        }

        $result = $this->find($data['client_id']);
        if (is_null($result)) {
            return $this->setError('账号不存在');
        }

        if (!hash_equals($result->getAttr('password'), user_md5($data['password_old']))) {
            return $this->setError('原始密码错误');
        }

        $result->setAttr('password', $data['password']);
        $result->save();

        Cache::tag('token:admin_' . $data['client_id'])->clear();
        $this->hasToken()->where(['client_id' => $data['client_id'], 'client_type' => 1])->delete();

        return true;
    }

    /**
     * 重置一个账号密码
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function resetAdminItem(array $data)
    {
        if (!$this->validateData($data, 'reset')) {
            return false;
        }

        if (!$this->checkAdminAuth($data['client_id'])) {
            return false;
        }

        // 初始化部分数据
        $data['password'] = mb_strtolower(get_randstr(8), 'utf-8');
        $map[] = ['admin_id', '=', $data['client_id']];

        self::update(['password' => $data['password']], $map);
        Cache::tag('token:admin_' . $data['client_id'])->clear();

        return ['password' => $data['password']];
    }

    /**
     * 批量删除账号
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delAdminList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        foreach ($data['client_id'] as $item) {
            if (!$this->checkAdminAuth($item)) {
                return false;
            }
        }

        $map = [['admin_id', 'in', $data['client_id']]];
        self::update(['is_delete' => 1], $map);

        foreach ($data['client_id'] as $value) {
            Cache::tag('token:admin_' . $value)->clear();
        }

        $map = [
            ['client_id', 'in', $data['client_id']],
            ['client_type', '=', 1],
        ];

        $this->hasToken()->where($map)->delete();
        return true;
    }

    /**
     * 获取一个账号
     * @access public
     * @param array $data 外部数据
     * @return array|false|null
     * @throws
     */
    public function getAdminItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $result = $this->with('get_auth_group')->find($data['client_id']);
        return is_null($result) ? null : $result->append(['last_ip_region'])->toArray();
    }

    /**
     * 获取账号列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAdminList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        !isset($data['client_id']) ?: $map[] = ['admin_id', 'in', $data['client_id']];
        empty($data['account']) ?: $map[] = ['username|nickname', '=', $data['account']];
        is_empty_parm($data['group_id']) ?: $map[] = ['group_id', '=', $data['group_id']];
        is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['admin_id' => 'desc'])
            ->with('get_auth_group')
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->append(['last_ip_region'])
            ->toArray();

        return $result;
    }

    /**
     * 获取指定账号的基础数据
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getAdminSelect(array $data)
    {
        if (!$this->validateData($data, 'select')) {
            return false;
        }

        $map[] = ['admin_id', 'in', $data['client_id']];
        $field = 'admin_id,username,nickname,status';

        $order = [];
        $result = $this->where($map)->column($field, 'admin_id');

        // 根据传入顺序返回列表
        foreach ($data['client_id'] as $value) {
            if (array_key_exists($value, $result)) {
                $order[] = $result[$value];
            }
        }

        return $order;
    }

    /**
     * 注销账号
     * @access public
     * @return bool
     */
    public function logoutAdmin(): bool
    {
        $map[] = ['client_id', '=', get_client_id()];
        $map[] = ['client_type', '=', 1];

        $token = input('param.token');
        if (!empty($token)) {
            $map[] = ['token', '=', $token];
            Cache::delete('token:' . $token);
        }

        $this->hasToken()->where($map)->delete();
        return true;
    }

    /**
     * 登录账号
     * @access public
     * @param array $data       外部数据
     * @param bool  $isGetToken 是否需要返回Token
     * @return array|false
     * @throws
     */
    public function loginAdmin(array $data, $isGetToken = true)
    {
        if (!$this->validateData($data, 'login')) {
            return false;
        }

        // 验证码识别
        $request = request();
        $appResult = App::getAppCaptcha($request->param('appkey'), false);

        if (false !== $appResult['captcha']) {
            $checkResult = \app\careyshop\service\App::checkCaptcha($request->param('login_code'));
            if (true !== $checkResult) {
                return $this->setError($checkResult);
            }
        }

        // 根据账号获取
        $map[] = ['username', '=', $data['username']];
        $result = $this->where($map)->find();

        if (is_null($result)) {
            return $this->setError('账号不存在');
        }

        if ($result->getAttr('status') !== 1) {
            return $this->setError('账号已禁用');
        }

        if (!hash_equals($result->getAttr('password'), user_md5($data['password']))) {
            return $this->setError('账号或密码错误');
        }

        $data['last_login'] = time();
        $data['last_ip'] = $request->ip();
        unset($data['admin_id']);
        $result->allowField(['last_login', 'last_ip'])->save($data);

        if (!$isGetToken) {
            return ['admin' => $result->toArray()];
        }

        $adminId = $result->getAttr('admin_id');
        $groupId = $result->getAttr('group_id');
        $tokenResult = Token::setToken($adminId, $groupId, 1, $data['username'], $data['platform']);

        Cache::tag('token:admin_' . $result->getAttr('admin_id'))->clear();
        return ['admin' => $result->toArray(), 'token' => $tokenResult];
    }

    /**
     * 刷新Token
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function refreshToken(array $data)
    {
        if (!$this->validateData($data, 'refresh')) {
            return false;
        }

        // 获取原始Token
        $oldToken = input('param.token', '');

        $tokenDb = new Token();
        $result = $tokenDb->refreshUser(1, $data['refresh'], $oldToken);

        if (false !== $result) {
            Cache::delete('token:' . $oldToken);
            return ['token' => $result];
        }

        return $this->setError($tokenDb->getError());
    }
}
