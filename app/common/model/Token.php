<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    Token模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/9/9
 */

namespace app\common\model;

class Token extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'token_id';

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'token_id',
        'client_id',
        'username',
        'client_type',
        'platform',
    ];

    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'token_id',
        'client_id',
        'client_type',
        'code',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'token_id'        => 'integer',
        'client_id'       => 'integer',
        'group_id'        => 'integer',
        'client_type'     => 'integer',
        'token_expires'   => 'integer',
        'refresh_expires' => 'integer',
    ];

    /**
     * 产生Token
     * @access public
     * @param int    $id       编号
     * @param int    $group    用户组编号
     * @param int    $type     顾客或管理组
     * @param string $username 账号
     * @param string $platform 来源平台
     * @return false|array
     */
    public static function setToken(int $id, int $group, int $type, string $username, string $platform)
    {
        $code = rand_string();
        $token = user_md5(sprintf('%d%d%s', $id, $type, $code));
        $expires = time() + (30 * 24 * 60 * 60); // 30天

        // 准备数据
        $data = [
            'client_id'       => $id,
            'group_id'        => $group,
            'username'        => $username,
            'client_type'     => $type,
            'platform'        => $platform,
            'code'            => $code,
            'token'           => $token,
            'token_expires'   => $expires,
            'refresh'         => user_md5(rand_string() . $token),
            'refresh_expires' => $expires + (1 * 24 * 60 * 60),
        ];

        // 搜索条件
        $map[] = ['client_id', '=', $id];
        $map[] = ['client_type', '=', $type];
        $map[] = ['platform', '=', $platform];

        $result = self::where($map)->findOrEmpty();
        $result->save($data);

        return $result->hidden(['username', 'platform'])->toArray();
    }

    /**
     * 刷新Token
     * @access public
     * @param int    $type     顾客或管理组
     * @param string $refresh  刷新令牌
     * @param string $oldToken 原授权令牌
     * @return false|array
     * @throws
     */
    public function refreshUser(int $type, string $refresh, string $oldToken)
    {
        // 搜索条件
        $map[] = ['client_id', '=', get_client_id()];
        $map[] = ['client_type', '=', $type];
        $map[] = ['token', '=', $oldToken];

        $result = $this->where($map)->find();
        if (is_null($result)) {
            return $this->setError('refresh不存在');
        }

        if (time() > $result->getAttr('refresh_expires')) {
            return $this->setError('refresh已过期');
        }

        if (!hash_equals($result->getAttr('refresh'), $refresh)) {
            return $this->setError('refresh错误');
        }

        // 准备更新数据
        $code = rand_string();
        $token = user_md5(sprintf('%d%d%s', get_client_id(), $type, $code));
        $expires = time() + (30 * 24 * 60 * 60); // 30天

        $data = [
            'code'            => $code,
            'token'           => $token,
            'token_expires'   => $expires,
            'refresh'         => user_md5(rand_string() . $token),
            'refresh_expires' => $expires + (1 * 24 * 60 * 60),
        ];

        $result->save($data);
        return $result->hidden(['username', 'platform'])->toArray();
    }
}
