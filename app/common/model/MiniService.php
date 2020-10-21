<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    微服务模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/13
 */

namespace app\common\model;

use think\facade\Cache;
use think\facade\Route;

class MiniService extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'mini_service_id';

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'mini_service_id',
        'code',
        'platform',
        'model',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'mini_service_id' => 'integer',
        'code'            => 'integer',
        'setting'         => 'array',
        'expand'          => 'array',
        'status'          => 'integer',
    ];

    /**
     * 生成唯一微服务code
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
     * 获取框架已支持的微服务
     * @access public
     * @return mixed
     */
    public function getMiniServiceExist()
    {
        return config('extra.mini_service', []);
    }

    /**
     * 获取某个平台下指定模块的默认配置结构
     * @access public
     * @param array $data       外部数据
     * @param false $isInternal 内部调用则不进行规则检测
     * @return array|false
     */
    public function getMiniServiceSetting(array $data, $isInternal = false)
    {
        if (!$isInternal && !$this->validateData($data, 'setting')) {
            return false;
        }

        // 默认配置结构
        switch ($data['platform']) {
            case 'wechat':
                $setting = [
                    'official_account' => [
                        'app_id'  => [
                            'name'        => '开发者ID',
                            'value'       => '',
                            'description' => '开发者ID来自于您申请的公众号AppID',
                            'validate'    => 'max:32',
                        ],
                        'secret'  => [
                            'name'        => '开发者密码',
                            'value'       => '',
                            'description' => '开发者密码来自于您申请的公众号AppSecret',
                            'validate'    => 'max:32',
                        ],
                        'url'     => [
                            'name'        => '服务器地址',
                            'value'       => '',
                            'description' => '仅支持80(http)和443(https)端口，不填写(推荐)则会自动生成',
                            'validate'    => 'url',
                        ],
                        'token'   => [
                            'name'        => '令牌',
                            'value'       => 'CareyShop',
                            'description' => '必须为英文或数字，长度为3-32字符，如不填写则默认为CareyShop',
                            'validate'    => 'alphaNum|length:3,32',
                        ],
                        'aes_key' => [
                            'name'        => '消息加解密密钥',
                            'value'       => '',
                            'description' => '由43位范围为A-Z，a-z，0-9字符随机组成，安全模式下必须填写',
                            'validate'    => 'alphaNum|length:43',
                        ],
                        'domain'  => [
                            'name'        => '安全域名',
                            'value'       => request()->host(),
                            'description' => '微信后台需要填写安全域名时，可使用此域名',
                            'validate'    => 'require',
                        ],
                    ],
                ];
                break;

            default:
                $setting = [];
        }

        if (!array_key_exists($data['model'], $setting)) {
            return $this->setError('配置结构不存在');
        }

        // 外部请求时删除验证规则
        $filter = function ($value) use ($isInternal) {
            if (!$isInternal) {
                foreach ($value as &$item) {
                    unset($item['validate']);
                }
            }

            return $value;
        };

        return $filter($setting[$data['model']]);
    }

    /**
     * 重构配置结构
     * @access private
     * @param array $source 来源数据
     * @return array|false
     */
    private function refactorSetting(array $source)
    {
        $data = $validate = [];
        if (!($setting = $this->getMiniServiceSetting($source, true))) {
            return false;
        }

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

        // 额外需要处理的数据(预留其余平台与模块)
        switch ($source['platform']) {
            case 'wechat':
                switch ($source['model']) {
                    case 'official_account':
                        if (!empty($source['code']) && empty($source['setting']['url']['value'])) {
                            $vars = [
                                'method' => 'put.wechat.data',
                                'code'   => $source['code'],
                            ];

                            $setting['url']['value'] = Route::buildUrl("api/{$this->version}/wechat", $vars)
                                ->domain(true)
                                ->build();
                        }
                }

                break;

            default:
                break;
        }

        return $setting;
    }

    /**
     * 添加一个微服务
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addMiniServiceItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 初始化部分数据
        $data['code'] = $this->getOfficialCode();
        unset($data['mini_service_id']);

        if (!($data['setting'] = $this->refactorSetting($data))) {
            return false;
        }

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个微服务
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setMiniServiceItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        if (!empty($data['name'])) {
            $map[] = ['mini_service_id', '<>', $data['mini_service_id']];
            $map[] = ['name', '=', $data['name']];

            if (self::checkUnique($map)) {
                return $this->setError('微服务名称已存在');
            }
        }

        $result = $this->where('mini_service_id', '=', $data['mini_service_id'])->find();
        if (is_null($result)) {
            return $this->setError('数据不存在');
        }

        if (!empty($data['setting'])) {
            // 对请求参数进行补齐
            $data['code'] = $result->getAttr('code');
            $data['platform'] = $result->getAttr('platform');
            $data['model'] = $result->getAttr('model');

            if (!($data['setting'] = $this->refactorSetting($data))) {
                return false;
            }
        }

        if ($result->save($data)) {
            Cache::tag('MiniService')->clear();
            return $result->toArray();
        }

        return false;
    }

    /**
     * 获取一个微服务
     * @access public
     * @param array $data 外部数据
     * @return array|false|null
     * @throws
     */
    public function getMiniServiceItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $result = $this->find($data['mini_service_id']);
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 获取微服务列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getMiniServiceList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['name']) ?: $map[] = ['name', 'like', '%' . $data['name'] . '%'];
        empty($data['platform']) ?: $map[] = ['platform', '=', $data['model']];
        empty($data['code']) ?: $map[] = ['code', '=', $data['code']];
        is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['mini_service_id' => 'desc'])
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        return $result;
    }

    /**
     * 批量删除微服务
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delMiniServiceList(array $data)
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        self::destroy($data['mini_service_id']);
        Cache::tag('MiniService')->clear();

        return true;
    }

    /**
     * 批量设置微服务状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setMiniServiceStatus(array $data)
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['mini_service_id', 'in', $data['mini_service_id']];
        self::update(['status' => $data['status']], $map);
        Cache::tag('MiniService')->clear();

        return true;
    }
}
