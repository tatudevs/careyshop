<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    公众号模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/13
 */

namespace app\common\model;

use think\facade\Cache;
use think\facade\Route;

class OfficialAccounts extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'official_accounts_id';

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'official_accounts_id',
        'code',
        'model',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'official_accounts_id' => 'integer',
        'code'                 => 'integer',
        'setting'              => 'array',
        'status'               => 'integer',
    ];

    /**
     * 生成唯一公众号code
     * @access private
     * @return string
     */
    private function getOfficialCode()
    {
        do {
            $code = rand_number(8);
        } while (self::checkUnique(['code' => $code]));

        return $code;
    }

    /**
     * 根据公众号所属模块获取默认配置结构
     * @access public
     * @param array $data       外部数据
     * @param false $isInternal 内部调用则不进行规则检测
     * @return false|string[][]
     */
    public function getOfficialSetting(array $data, $isInternal = false)
    {
        if (!$isInternal && !$this->validateData($data, 'setting')) {
            return false;
        }

        // 默认配置结构
        $setting = [
            'wechat' => [
                'app_id'           => [
                    'name'        => '开发者ID',
                    'value'       => '',
                    'description' => '开发者ID来自于您申请的公众号AppID',
                    'validate'    => 'max:32',
                ],
                'app_secret'       => [
                    'name'        => '开发者密码',
                    'value'       => '',
                    'description' => '开发者密码来自于您申请的公众号AppSecret',
                    'validate'    => 'max:32',
                ],
                'url'              => [
                    'name'        => '服务器地址',
                    'value'       => '',
                    'description' => '仅支持80(http)和443(https)端口，不填写则由服务端生成',
                    'validate'    => 'url',
                ],
                'token'            => [
                    'name'        => '令牌',
                    'value'       => 'CareyShop',
                    'description' => '必须为英文或数字，长度为3-32字符，如不填写则默认为CareyShop',
                    'validate'    => 'alphaNum|length:3,32',
                ],
                'encoding_aes_key' => [
                    'name'        => '消息加解密密钥',
                    'value'       => '',
                    'description' => '由43位范围为A-Z，a-z，0-9字符随机组成，安全模式下必须填写',
                    'validate'    => 'alphaNum|length:43',
                ],
            ],
        ];

        // 外部请求时不带入检测规则
        $filter = function ($value) use ($isInternal) {
            if (!$isInternal) {
                foreach ($value as &$item) {
                    unset($item['validate']);
                }
            }

            return $value;
        };

        return isset($setting[$data['model']])
            ? $filter($setting[$data['model']])
            : $this->setError('所属模块 ' . $data['model'] . ' 不存在默认配置结构');
    }

    /**
     * 重构公众号配置结构
     * @access private
     * @param array $source 来源数据
     * @return false|string[][]
     */
    private function refactorSetting(array $source)
    {
        $data = $validate = [];
        $setting = $this->getOfficialSetting($source, true);

        if (!$setting) return false;

        foreach ($setting as $key => &$item) {
            if (isset($source['setting'][$key]['value'])) {
                $item['value'] = $source['setting'][$key]['value'];
            }

            $data[$key] = $item['value'];
            $validate[$key . '|' . $item['name']] = $item['validate'];
            unset($item['validate']);
        }

        if (!$this->validateData($data, null, false, $validate)) {
            return false;
        }

        // 生成服务器地址
        if (!empty($source['code']) && empty($setting['url']['value'])) {
            $vars = [
                'method' => 'put.official.wechat.data',
                'code'   => $source['code'],
            ];

            $setting['url']['value'] = Route::buildUrl('api/v1/official_wechat', $vars)
                ->domain(true)
                ->build();
        }

        return $setting;
    }

    /**
     * 添加一个公众号
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addOfficialItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 初始化部分数据
        $data['code'] = $this->getOfficialCode();
        unset($data['official_accounts_id']);

        if (false === ($data['setting'] = $this->refactorSetting($data))) {
            return false;
        }

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个公众号
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setOfficialItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        if (!empty($data['name'])) {
            $map[] = ['official_accounts_id', '<>', $data['official_accounts_id']];
            $map[] = ['name', '=', $data['name']];

            if (self::checkUnique($map)) {
                return $this->setError('公众号名称已存在');
            }
        }

        $map = [['official_accounts_id', '=', $data['official_accounts_id']]];
        $result = $this->where($map)->find();

        if (is_null($result)) {
            return $this->setError('数据不存在');
        }

        if (!empty($data['setting'])) {
            // 对请求参数进行补齐
            $data['code'] = $result->getAttr('code');
            $data['model'] = $result->getAttr('model');

            if (false === ($data['setting'] = $this->refactorSetting($data))) {
                return false;
            }
        }

        if ($result->save($data)) {
            Cache::tag('OfficialAccounts')->clear();
            return $result->toArray();
        }

        return false;
    }

    /**
     * 获取一个公众号
     * @access public
     * @param array $data 外部数据
     * @return array|false|null
     * @throws
     */
    public function getOfficialItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $result = $this->find($data['official_accounts_id']);
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 获取公众号列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getOfficialList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['name']) ?: $map[] = ['name', 'like', '%' . $data['name'] . '%'];
        empty($data['code']) ?: $map[] = ['code', '=', $data['code']];
        empty($data['model']) ?: $map[] = ['model', '=', $data['model']];
        is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['official_accounts_id' => 'desc'])
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        return $result;
    }

    /**
     * 批量删除公众号
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delOfficialList(array $data)
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        self::destroy($data['official_accounts_id']);
        Cache::tag('OfficialAccounts')->clear();

        return true;
    }

    /**
     * 批量设置公众号状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setOfficialStatus(array $data)
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['official_accounts_id', 'in', $data['official_accounts_id']];
        self::update(['status' => $data['status']], $map);
        Cache::tag('OfficialAccounts')->clear();

        return true;
    }
}
