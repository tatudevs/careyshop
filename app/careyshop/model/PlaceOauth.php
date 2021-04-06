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
     * @var array|string
     */
    protected $pk = 'place_oauth_id';

    /**
     * 只读属性
     * @var mixed|string[]
     */
    protected $readonly = [
        'place_oauth_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var mixed|string[]
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
        !is_empty_parm($data['code']) ?: $data['code'] = '';

        // 检测是否存在相同配置
        $map[] = ['model', '=', $data['model']];
        $map[] = ['code', '=', $data['code']];

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

        !is_empty_parm($data['code']) ?: $data['code'] = '';
        $map[] = ['code', '=', $data['code']];

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
     * @return array|false
     */
    public function getPlaceOAuthItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        return $this->findOrEmpty($data['place_oauth_id'])->toArray();
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
        empty($data['code']) ?: $map[] = ['code', '=', $data['code']];
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
        $map[] = ['code', '=', is_empty_parm($data['code']) ? '' : $data['code']];
        $map[] = ['status', '=', 1];

        // 动态获取授权地址
        $attr = function ($value, $data) {
            $vars = [
                'method' => 'authorize',
                'code'   => $data['code'],
                'model'  => $data['model'],
                'value'  => $value,
            ];

            return Route::buildUrl("api/{$this->version}/place_oauth", $vars)->domain(true)->build();
        };

        return $this->cache(true, null, 'oauth')
            ->field('name,model,code,logo,icon')
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

    /**
     * 获取某个模块下的配置参数
     * @access private
     * @param string $model 所属模块
     * @param string $code  对应渠道(自定义)
     * @return array|bool
     * @throws
     */
    private function getOAuthConfig(string $model, string $code)
    {
        // 搜索条件
        $map[] = ['model', '=', $model];
        $map[] = ['code', '=', $code];
        $map[] = ['status', '=', 1];

        $result = $this->where($map)->field('client_id,client_secret,config,expand')->find();
        if (is_null($result)) {
            return $this->setError('OAuth模型不存在或已停用');
        }

        // 提取配置并尝试合并扩展配置
        $config = @json_decode($result->getAttr('config'), true);
        $expand = @json_decode($result->getAttr('expand'), true);
        $basics = $result->hidden(['expand', 'config'])->toArray();

        if (is_array($expand)) {
            $basics = array_merge($expand, $basics);
        }

        // 配置回调地址
        $vars = ['method' => 'callback', 'code' => $code, 'model' => $model];
        $basics['redirect'] = Route::buildUrl("api/{$this->version}/place_oauth", $vars)->domain(true)->build();

        return [$model => $basics, 'config' => $config];
    }

    /**
     * OAuth2.0授权准备
     * @access public
     * @param array $data 外部数据
     * @return false|string
     */
    public function authorizeOAuth(array $data)
    {
        if (!$this->validateData($data, 'authorize')) {
            return false;
        }

        !is_empty_parm($data['code']) ?: $data['code'] = '';
        $basics = $this->getOAuthConfig($data['model'], $data['code']);

        if (!$basics) {
            return false;
        }

        $service = new \app\careyshop\service\PlaceOauth($data['model'], $basics);
        $result = $service->getAuthorizeRedirect();

        return false === $result ? $service->getError() : $result;
    }

    public function callbackOAuth(array $data)
    {
    }
}
