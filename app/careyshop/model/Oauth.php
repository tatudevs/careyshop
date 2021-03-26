<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    OAuth2.0模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/3/26
 */

namespace app\careyshop\model;

class Oauth extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'oauth_id';

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'oauth_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'oauth_id' => 'integer',
        'status'   => 'integer',
    ];

    /**
     * 添加一条授权机制
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addOAuthItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['oauth_id']);

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一条授权机制
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setOAuthItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        $map[] = ['oauth_id', '=', $data['oauth_id']];
        $result = self::update($data, $map);

        return $result->toArray();
    }

    /**
     * 批量删除授权机制
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delOAuthList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        self::destroy($data['oauth_id']);
        return true;
    }

    /**
     * 获取一条授权机制
     * @access public
     * @param array $data 外部数据
     * @return array|false|null
     * @throws
     */
    public function getOAuthItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $result = $this->find($data['oauth_id']);
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 获取授权机制列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getOAuthList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['name']) ?: $map[] = ['name', 'like', '%' . $data['name'] . '%'];
        empty($data['model']) ?: $map[] = ['model', '=', $data['model']];
        empty($data['terminal']) ?: $map[] = ['terminal', '=', $data['terminal']];
        is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];

        return $this->where($map)->select()->toArray();
    }

    /**
     * 获取可使用的授权机制列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getOAuthType(array $data)
    {
        if (!$this->validateData($data, 'type')) {
            return false;
        }

        // 搜索条件
        $map[] = ['terminal', '=', $data['terminal']];
        $map[] = ['status', '=', 1];

        // 返回字段
        $field = 'name,model,logo,icon';

        return $this->field($field)->where($map)->select()->toArray();
    }
}
