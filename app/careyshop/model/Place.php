<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    渠道平台模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/13
 */

namespace app\careyshop\model;

use think\facade\Cache;
use think\facade\Route;

class Place extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'place_id';

    /**
     * 只读属性
     * @var string[]
     */
    protected $readonly = [
        'place_id',
        'code',
        'platform',
        'model',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'place_id' => 'integer',
        'code'     => 'integer',
        'setting'  => 'array',
        'expand'   => 'array',
        'status'   => 'integer',
    ];

    /**
     * 生成唯一渠道Code
     * @access private
     * @return string
     */
    private function getPlaceCode(): string
    {
        do {
            $code = rand_number();
        } while (self::checkUnique(['code' => $code]));

        return $code;
    }

    /**
     * 获取框架已支持的渠道平台
     * @access public
     * @return mixed
     */
    public function getPlaceExist()
    {
        return config('extra.place', []);
    }

    /**
     * 获取某个平台下指定模块的默认配置结构
     * @access public
     * @param array $data       外部数据
     * @param false $isInternal 内部调用则不进行规则检测
     * @return array|false
     */
    public function getPlaceSetting(array $data, $isInternal = false)
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
        if (!($setting = $this->getPlaceSetting($source, true))) {
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

                            $setting['url']['value'] = Route::buildUrl("api/$this->version/wechat", $vars)
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
     * 添加一个渠道平台
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addPlaceItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 初始化部分数据
        $data['code'] = $this->getPlaceCode();
        unset($data['place_id']);

        if (!($data['setting'] = $this->refactorSetting($data))) {
            return false;
        }

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个渠道平台
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setPlaceItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        if (!empty($data['name'])) {
            $map[] = ['place_id', '<>', $data['place_id']];
            $map[] = ['name', '=', $data['name']];

            if (self::checkUnique($map)) {
                return $this->setError('渠道平台名称已存在');
            }
        }

        $result = $this->where('place_id', '=', $data['place_id'])->find();
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
            Cache::tag('Place')->clear();
            return $result->toArray();
        }

        return false;
    }

    /**
     * 获取一个渠道平台
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getPlaceItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        return $this->findOrEmpty($data['place_id'])->toArray();
    }

    /**
     * 获取渠道平台列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getPlaceList(array $data)
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
        $result['items'] = $this->setDefaultOrder(['place_id' => 'desc'])
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        return $result;
    }

    /**
     * 批量删除渠道平台
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delPlaceList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        self::destroy($data['place_id']);
        Cache::tag('Place')->clear();

        return true;
    }

    /**
     * 批量设置渠道平台状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setPlaceStatus(array $data): bool
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['place_id', 'in', $data['place_id']];
        self::update(['status' => $data['status']], $map);
        Cache::tag('Place')->clear();

        return true;
    }

    /**
     * 获取渠道的授权机制
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getPlaceOAuth(array $data)
    {
        if (!$this->validateData($data, 'oauth')) {
            return false;
        }

        $placeID = $this->where('code', '=', $data['code'])->value('place_id');
        if (!$placeID) {
            return $this->setError('当前渠道不存在');
        }

        // 搜索条件
        $map[] = ['place_id', '=', $placeID];
        $map[] = ['status', '=', 1];

        // 动态获取授权地址
        $attr = function ($value, $data) {
            $vars = [
                'method'         => 'authorize',
                'place_oauth_id' => $data['place_oauth_id'],
                'value'          => $value,
            ];

            return Route::buildUrl("api/$this->version/place_oauth", $vars)->domain(true)->build();
        };

        $oauthDB = new PlaceOauth();
        return $oauthDB->cache(true, null, 'oauth')
            ->field('name,model,place_oauth_id,logo,icon')
            ->withAttr('authorize', $attr)
            ->where($map)
            ->select()
            ->append(['authorize'])
            ->hidden(['place_oauth_id'])
            ->toArray();
    }
}
