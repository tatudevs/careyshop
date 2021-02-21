<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    应用安装包模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/24
 */

namespace app\careyshop\model;

use think\facade\Cache;
use think\facade\Config;

class AppInstall extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'app_install_id';

    /**
     * 是否需要自动写入时间戳
     * @var bool|string
     */
    protected $autoWriteTimestamp = true;

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'app_install_id',
        'create_time',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'app_install_id' => 'integer',
        'count'          => 'integer',
    ];

    /**
     * 系统标识修改器
     * @access public
     * @param string $value 值
     * @return string
     */
    public function setUserAgentAttr(string $value): string
    {
        return mb_strtolower($value, 'utf-8');
    }

    /**
     * 添加一个应用安装包
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addAppInstallItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关数据
        unset($data['app_install_id'], $data['count']);

        if ($this->save($data)) {
            Cache::tag('AppInstall')->clear();
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个应用安装包
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setAppInstallItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        $field = ['user_agent', 'name', 'ver', 'url'];
        $map[] = ['app_install_id', '=', $data['app_install_id']];

        $result = self::update($data, $map, $field);
        Cache::tag('AppInstall')->clear();

        return $result->toArray();
    }

    /**
     * 获取一个应用安装包
     * @access public
     * @param array $data 外部数据
     * @return array|false|null
     * @throws
     */
    public function getAppInstallItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $result = $this->find($data['app_install_id']);
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 批量删除应用安装包
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delAppInstallList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        self::destroy($data['app_install_id']);
        Cache::tag('AppInstall')->clear();

        return true;
    }

    /**
     * 获取应用安装包列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAppInstallList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['user_agent']) ?: $map[] = ['user_agent', '=', $data['user_agent']];
        empty($data['name']) ?: $map[] = ['name', 'like', '%' . $data['name'] . '%'];

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['app_install_id' => 'desc'])
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        return $result;
    }

    /**
     * 根据条件查询是否有更新
     * @access public
     * @param array $data 外部数据
     * @return array|bool|false[]
     * @throws
     */
    public function queryAppInstallUpdated(array $data)
    {
        if (!$this->validateData($data, 'updated')) {
            return false;
        }

        // 搜索条件
        $map[] = ['user_agent', '=', $data['user_agent']];

        // 实际查询
        $result = $this->cache(true, null, 'AppInstall')
            ->field('name,ver,url')
            ->where($map)
            ->select();

        foreach ($result as $value) {
            if (version_compare($value->getAttr('ver'), $data['ver'], '>')) {
                $value->setAttr('is_updated', true);
                return $value->toArray();
            }
        }

        return ['is_updated' => false];
    }

    /**
     * 根据请求获取一个应用安装包
     * @access public
     * @return array|mixed
     */
    public function requestAppInstallItem(): array
    {
        // 获取所有安装包列表
        $result = $this->cache(true, null, 'AppInstall')
            ->column('user_agent,ver,url', 'app_install_id');

        $data = [];
        $maxVersion = '';
        $agent = request()->server('HTTP_USER_AGENT');

        foreach ($result as $value) {
            if (false !== mb_stripos($agent, $value['user_agent'], null, 'utf-8')) {
                // 获取最新版本号的安装包
                if (version_compare($value['ver'], $maxVersion, '>=')) {
                    $maxVersion = $value['ver'];
                    $data = $value;
                }
            }
        }

        // 后续处理数据
        if (!empty($data)) {
            // 如果是安卓微信,则返回自定义中间页
            if (mb_stripos($agent, 'Android', null, 'utf-8') && mb_stripos($agent, 'MicroMessenger', null, 'utf-8')) {
                $data['url'] = Config::get('careyshop.system_info.weixin_url', '');
            }

            // 自增访问次数
            $map[] = ['app_install_id', '=', $data['app_install_id']];
            $this->where($map)->inc('count')->update();
        }

        return $data;
    }
}
