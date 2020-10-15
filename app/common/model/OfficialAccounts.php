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
     * @return array|false
     */
    public function getOfficialStting(array $data, $isInternal = false)
    {
        if (!$isInternal && !$this->validateData($data, 'setting')) {
            return false;
        }

        // 默认配置结构
        $stting = [
            'wechat' => [
                'app_id'           => [
                    'name'        => '开发者ID',
                    'value'       => '',
                    'description' => '开发者ID来自于您申请的公众号AppID',
                ],
                'app_secret'       => [
                    'name'        => '开发者密码',
                    'value'       => '',
                    'description' => '开发者密码来自于您申请的公众号AppSecret',
                ],
                'url'              => [
                    'name'        => '服务器地址',
                    'value'       => '',
                    'description' => '仅支持80(http)和443(https)端口，不填写则由服务端生成',
                ],
                'token'            => [
                    'name'        => '令牌',
                    'value'       => 'CareyShop',
                    'description' => '必须为英文或数字，长度为3-32字符，如不填写则默认为CareyShop',
                ],
                'encoding_aes_key' => [
                    'name'        => '消息加解密密钥',
                    'value'       => '',
                    'description' => '由43位字符组成，可随机修改，字符范围为A-Z，a-z，0-9，安全模式下必须填写',
                ],
            ],
        ];

        // 选择需要返回的结构
        $result = isset($stting[$data['model']]) ? $stting[$data['model']] : [];

        // 生成服务器地址
        if (!empty($data['code']) && empty($data['url'])) {
            $vars = [
                'method' => 'put.official.wechat.data',
                'code'   => $data['code'],
            ];

            $result['url']['value'] = Route::buildUrl('api/v1/official_wechat', $vars)
                ->domain(true)
                ->build();
        }

        return $result;
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
        !empty($data['setting']) ?: $data['setting'] = [];
        unset($data['official_accounts_id']);

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

        if (isset($data['setting']) && '' == $data['setting']) {
            $data['setting'] = [];
        }

        // 允许修改字段与条件
        $field = ['name', 'remark', 'setting', 'status'];
        $map = [['official_accounts_id', '=', $data['official_accounts_id']]];

        $result = self::update($data, $map, $field);
        return $result->toArray();
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

        return true;
    }
}
