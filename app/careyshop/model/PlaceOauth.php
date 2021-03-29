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

use think\facade\Cache;
use think\facade\Route;

class PlaceOauth extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'place_oauth_id';

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'place_oauth_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'place_oauth_id' => 'integer',
        'status'         => 'integer',
    ];

    /**
     * 添加一条授权机制
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addPlaceOAuthItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['place_oauth_id']);
        !is_empty_parm($data['channel']) ?: $data['channel'] = '';

        // 检测是否存在相同配置
        $map[] = ['model', '=', $data['model']];
        $map[] = ['channel', '=', $data['channel']];

        if (self::checkUnique($map)) {
            return $this->setError('相同模块下已存在重复的渠道');
        }

        if ($this->save($data)) {
            Cache::tag('oauth')->clear();
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
    public function setPlaceOAuthItem(array $data)
    {
        if (!$this->validateData($data, 'set')) {
            return false;
        }

        // 检测是否存在相同配置
        $map[] = ['place_oauth_id', '<>', $data['place_oauth_id']];
        $map[] = ['model', '=', $data['model']];

        !is_empty_parm($data['channel']) ?: $data['channel'] = '';
        $map[] = ['channel', '=', $data['channel']];

        if (self::checkUnique($map)) {
            return $this->setError('相同模块下已存在重复的渠道');
        }

        // 实际更新数据
        $map = [['place_oauth_id', '=', $data['place_oauth_id']]];
        $result = self::update($data, $map);
        Cache::tag('oauth')->clear();

        return $result->toArray();
    }

    /**
     * 批量删除授权机制
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delPlaceOAuthList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        self::destroy($data['place_oauth_id']);
        Cache::tag('oauth')->clear();

        return true;
    }

    /**
     * 获取一条授权机制
     * @access public
     * @param array $data 外部数据
     * @return array|false|null
     * @throws
     */
    public function getPlaceOAuthItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $result = $this->find($data['place_oauth_id']);
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 获取授权机制列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getPlaceOAuthList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['name']) ?: $map[] = ['name', 'like', '%' . $data['name'] . '%'];
        empty($data['model']) ?: $map[] = ['model', '=', $data['model']];
        empty($data['channel']) ?: $map[] = ['channel', '=', $data['channel']];
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
    public function getPlaceOAuthType(array $data)
    {
        if (!$this->validateData($data, 'type')) {
            return false;
        }

        // 搜索条件
        $map[] = ['channel', '=', is_empty_parm($data['channel']) ? '' : $data['channel']];
        $map[] = ['status', '=', 1];

        // 动态获取授权地址
        $attr = function ($value, $data) {
            $vars = [
                'method'  => 'authorize',
                'channel' => $data['channel'],
                'model'   => $data['model'],
            ];

            return Route::buildUrl("api/{$this->version}/place_oauth", $vars)->domain(true)->build();
        };

        return $this->cache(true, null, 'oauth')
            ->field('name,model,channel,logo,icon')
            ->withAttr('authorize', $attr)
            ->where($map)
            ->select()
            ->append(['authorize'])
            ->toArray();
    }

    /**
     * 批量设置授权机制状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setPlaceOAuthStatus(array $data): bool
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['app_id', 'in', $data['app_id']];
        self::update(['status' => $data['status']], $map);
        Cache::tag('oauth')->clear();

        return true;
    }

    public function authorizeOAuth(array $data)
    {
    }

    public function callbackOAuth(array $data)
    {
    }
}
