<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    账号管理模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/1
 */

namespace app\careyshop\model;

use careyshop\Ip2Region;
use think\facade\Cache;
use think\facade\Event;

class User extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'user_id';

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
        'user_id',
        'username',
        'create_time',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'user_id'         => 'integer',
        'is_mobile'       => 'integer',
        'is_email'        => 'integer',
        'sex'             => 'integer',
        'user_level_id'   => 'integer',
        'user_address_id' => 'integer',
        'group_id'        => 'integer',
        'last_login'      => 'timestamp',
        'status'          => 'integer',
        'is_delete'       => 'integer',
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
     * @param mixed $value 值
     * @return string
     */
    public function setPasswordAttr($value): string
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
        return $this->hasOne(Token::class, 'user_id', 'client_id');
    }

    /**
     * hasOne cs_user_money
     * @access public
     * @return object
     */
    public function hasUserMoney(): object
    {
        return $this->hasOne(UserMoney::class);
    }

    /**
     * hasOne cs_user_money
     * @access public
     * @return object
     */
    public function getUserMoney(): object
    {
        return $this
            ->hasOne(UserMoney::class)
            ->field('user_id,total_money,balance,lock_balance,points,lock_points')
            ->hidden(['user_id']);
    }

    /**
     * hasOne cs_user_level
     * @access public
     * @return object
     */
    public function getUserLevel(): object
    {
        return $this
            ->hasOne(UserLevel::class, 'user_level_id', 'user_level_id')
            ->joinType('left')
            ->field('user_level_id,name,icon,discount')
            ->hidden(['user_level_id']);
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
    public function getGetUserLevelAttr($value = null)
    {
        return $value ?? new \stdClass;
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
     * 注册一个新账号
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function addUserItem(array $data): bool
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 开启事务
        $this->startTrans();

        try {
            // 管理组可自定义组
            $data['group_id'] ??= AUTH_CLIENT;

            // 顾客组采用固定组
            is_client_admin() ?: $data['group_id'] = AUTH_CLIENT;
            $data['level_icon'] = UserLevel::where('user_level_id', '=', 1)->value('icon', '');

            $field = [
                'password', 'head_pic', 'sex', 'birthday', 'level_icon',
                'username', 'mobile', 'email', 'nickname', 'group_id',
            ];

            // 增加主数据
            $this->allowField($field)->save($data);

            // 添加资金表
            $this->hasUserMoney()->save([]);

            $this->commit();
            Event::trigger('UserRegister', [
                'user_id'  => $this->getAttr('user_id'),
                'password' => $data['password'],
            ]);

            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 编辑一个账号
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setUserItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        $map = ['user_id' => is_client_admin() ? (int)$data['client_id'] : get_client_id()];
        $field = ['group_id', 'nickname', 'head_pic', 'sex', 'birthday', 'status'];

        if (!is_client_admin()) {
            unset($data['password']);
            unset($data['status']);
            unset($data['group_id']);
        } else {
            if (!empty($data['password']) || isset($data['group_id'])) {
                array_push($field, 'password');
                Cache::tag('token:user_' . $map['user_id'])->clear();
                $this->hasToken()->where(['client_id' => $map['user_id'], 'client_type' => 0])->delete();
            }
        }

        $result = self::update($data, $map, $field);
        return $result->toArray();
    }

    /**
     * 批量设置账号状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setUserStatus(array $data): bool
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $idList = is_client_admin() ? $data['client_id'] : [0];
        $map[] = ['user_id', 'in', $idList];
        self::update(['status' => $data['status']], $map);

        foreach ($idList as $value) {
            Cache::tag('token:user_' . $value)->clear();
        }

        $map = [
            ['client_id', 'in', $idList],
            ['client_type', '=', 0],
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
    public function setUserPassword(array $data): bool
    {
        if (!$this->validateData($data, 'change')) {
            return false;
        }

        // 获取实际账号Id
        $userId = is_client_admin() ? $data['client_id'] : get_client_id();

        // 获取账号数据
        $result = $this->find($userId);
        if (is_null($result)) {
            return $this->setError('账号不存在');
        }

        if (!is_client_admin()) {
            if (empty($data['password_old'])) {
                return $this->setError('原始密码不能为空');
            }

            if (!hash_equals($result->getAttr('password'), user_md5($data['password_old']))) {
                return $this->setError('原始密码错误');
            }
        }

        $result->setAttr('password', $data['password']);
        $result->save();

        $this->hasToken()->where(['client_id' => $userId, 'client_type' => 0])->delete();
        Cache::tag('token:user_' . $userId)->clear();
        Event::trigger('ChangePassword', ['user_id' => $userId]);

        return true;
    }

    /**
     * 批量删除账号
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delUserList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        $idList = is_client_admin() ? $data['client_id'] : [0];
        $map[] = ['user_id', 'in', $idList];
        self::update(['is_delete' => 1], $map);

        foreach ($idList as $value) {
            Cache::tag('token:user_' . $value)->clear();
        }

        $map = [
            ['client_id', 'in', $idList],
            ['client_type', '=', 0],
        ];

        $this->hasToken()->where($map)->delete();
        return true;
    }

    /**
     * 获取一个账号
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getUserItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        return $this->with(['get_user_money', 'get_user_level', 'get_auth_group'])
            ->findOrEmpty(is_client_admin() ? $data['client_id'] : get_client_id())
            ->append(['last_ip_region'])
            ->toArray();
    }

    /**
     * 获取一个账号的简易信息
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getUserInfo(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        return $this->findOrEmpty(is_client_admin() ? $data['client_id'] : get_client_id())
            ->append(['last_ip_region'])
            ->toArray();
    }

    /**
     * 获取账号列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getUserList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        !isset($data['client_id']) ?: $map[] = ['user_id', 'in', $data['client_id']];
        empty($data['account']) ?: $map[] = ['username|mobile', '=', $data['account']];
        is_empty_parm($data['user_level_id']) ?: $map[] = ['user_level_id', '=', $data['user_level_id']];
        is_empty_parm($data['group_id']) ?: $map[] = ['group_id', '=', $data['group_id']];
        is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['user_id' => 'desc'])
            ->with(['get_user_level', 'get_auth_group'])
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
    public function getUserSelect(array $data)
    {
        if (!$this->validateData($data, 'select')) {
            return false;
        }

        $map[] = ['user_id', 'in', $data['client_id']];
        $field = 'user_id,username,nickname,mobile,status';

        $order = [];
        $result = $this->where($map)->column($field, 'user_id');

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
    public function logoutUser(): bool
    {
        $map[] = ['client_id', '=', get_client_id()];
        $map[] = ['client_type', '=', 0];

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
     * @param bool  $isInline   是否内联登录(不需要验证码与密码)
     * @return array|false
     * @throws
     */
    public function loginUser(array $data, $isGetToken = true, $isInline = false)
    {
        if (!$this->validateData($data, 'login')) {
            return false;
        }

        // 请求实列
        $request = request();

        // 图像验证码识别
        if (!$isInline) {
            $appResult = App::getAppCaptcha($request->param('appkey'), false);
            if (false !== $appResult['captcha']) {
                $checkResult = \app\careyshop\service\App::checkCaptcha($request->param('login_code'));
                if (true !== $checkResult) {
                    return $this->setError($checkResult);
                }
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

        if (!$isInline) {
            if (empty($data['password'])) {
                return $this->setError('密码不能为空');
            }

            if (!hash_equals($result->getAttr('password'), user_md5($data['password']))) {
                return $this->setError('账号或密码错误');
            }
        }

        $data['last_login'] = time();
        $data['last_ip'] = $request->ip();
        unset($data['user_id']);
        $result->allowField(['last_login', 'last_ip'])->save($data);
        $userData = $result->toArray();

        if (!$isGetToken) {
            return ['user' => $userData];
        }

        $userId = $userData['user_id'];
        $tokenResult = Token::setToken($userId, $userData['group_id'], 0, $data['username'], $data['platform']);

        Cache::tag('token:user_' . $userId)->clear();
        Event::trigger('UserLogin', ['user_id' => $userId]);

        return ['user' => $userData, 'token' => $tokenResult];
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
        $result = $tokenDb->refreshUser(0, $data['refresh'], $oldToken);

        if (false !== $result) {
            Cache::delete('token:' . $oldToken);
            return ['token' => $result];
        }

        return $this->setError($tokenDb->getError());
    }

    /**
     * 忘记密码
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function findUserPassword(array $data): bool
    {
        if (!$this->validateData($data, 'find')) {
            return false;
        }

        $map[] = ['username', '=', $data['username']];
        $result = $this->where($map)->find();

        if (is_null($result)) {
            return $this->setError('账号不存在');
        }

        if ($result->getAttr('status') !== 1) {
            return $this->setError('账号已禁用');
        }

        if (isset($data['mobile'])) {
            if ($result->getAttr('is_mobile') !== 1) {
                return $this->setError('当前账号未绑定手机号码，请联系客服找回！');
            }

            if ($result->getAttr('mobile') != $data['mobile']) {
                return $this->setError('手机号码错误');
            }
        } else {
            if ($result->getAttr('is_email') !== 1) {
                return $this->setError('当前账号未绑定邮件地址，请联系客服找回！');
            }

            if ($result->getAttr('email') != $data['email']) {
                return $this->setError('邮件地址错误');
            }
        }

        if (!Verification::useVerificationItem($data['mobile'] ?? $data['email'], $data['code'])) {
            return false;
        }

        $result->setAttr('password', $data['password']);
        $result->save();

        Cache::tag('token:user_' . $result->getAttr('user_id'))->clear();
        $this->hasToken()->where(['client_id' => $result->getAttr('user_id'), 'client_type' => 0])->delete();

        return true;
    }

    /**
     * 重新绑定手机或邮箱
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function setUserBind(array $data): bool
    {
        if (!$this->validateData($data, 'bind')) {
            return false;
        }

        if (AUTH_CLIENT !== get_client_group()) {
            return $this->setError('只允许顾客组使用此接口');
        }

        $number = $data['mobile'] ?? $data['email'];
        $type = !empty($data['mobile']) ? 'mobile' : 'email';

        $map[] = ['user_id', '<>', get_client_id()];
        $map[] = [$type, '=', $number];

        if (self::checkUnique($map)) {
            return $this->setError(('mobile' === $type ? '手机号码' : '邮件地址') . '已被占用');
        }

        if (!Verification::useVerificationItem($number, $data['code'])) {
            return false;
        }

        $map = [['user_id', '=', get_client_id()]];
        self::update([$type => $number, 'is_' . $type => 1], $map);

        return true;
    }
}
