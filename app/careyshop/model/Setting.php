<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    系统配置模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/28
 */

namespace app\careyshop\model;

use think\facade\{Cache, Config};

class Setting extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'setting_id';

    /**
     * 只读属性
     * @var string[]
     */
    protected $readonly = [
        'setting_id',
        'code',
        'module',
        'description',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'setting_id' => 'integer',
    ];

    /**
     * 获取某个模块的设置
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getSettingList(array $data)
    {
        if (!$this->validateData($data, 'get')) {
            return false;
        }

        $map[] = ['module', '=', $data['module']];
        empty($data['code']) ?: $map[] = ['code', 'in', $data['code']];

        $result = $this->where($map)->column('code,value,module,description,help_text', 'code');
        foreach ($result as &$value) {
            $temp = json_decode($value['value'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value['value'] = $temp;
            }
        }

        return $result;
    }

    /**
     * 设置某个模块下的配置参数
     * @access private
     * @param string $key    键名
     * @param mixed  $value  值
     * @param string $module 模块
     * @param string $scene  验证场景
     * @param bool   $toJson 是否转为json
     * @throws
     */
    private function setSettingItem(string $key, $value, string $module, string $scene, $toJson = false)
    {
        $data = ['value' => $value];
        if (!$this->validateData($data, $scene)) {
            throw new \Exception($key . $this->getError());
        }

        $map[] = ['code', '=', $key];
        $map[] = ['module', '=', $module];

        !$toJson ?: $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        self::update(['value' => $value], $map);
    }

    /**
     * 设置配送轨迹
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setDeliveryDistList(array $data): bool
    {
        if (!$this->validateData($data, 'rule')) {
            return false;
        }

        // 开启事务
        $this->startTrans();

        try {
            foreach ($data['data'] as $key => $value) {
                switch ($key) {
                    case 'api_id':
                    case 'api_key':
                        $this->setSettingItem($key, $value, 'delivery_dist', 'string');
                        break;

                    case 'is_sub':
                    case 'is_subscriber':
                        $this->setSettingItem($key, $value, 'delivery_dist', 'status');
                        break;

                    default:
                        throw new \Exception('键名' . $key . '不在允许范围内');
                }
            }

            $this->commit();
            Cache::tag('setting')->clear();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 设置支付页面
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setPaymentList(array $data): bool
    {
        if (!$this->validateData($data, 'rule')) {
            return false;
        }

        // 开启事务
        $this->startTrans();

        try {
            foreach ($data['data'] as $key => $value) {
                switch ($key) {
                    case 'success':
                    case 'error':
                        $this->setSettingItem($key, $value, 'payment', 'string');
                        break;

                    default:
                        throw new \Exception('键名' . $key . '不在允许范围内');
                }
            }

            $this->commit();
            Cache::tag('setting')->clear();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 设置配送优惠
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setDeliveryList(array $data): bool
    {
        if (!$this->validateData($data, 'rule')) {
            return false;
        }

        // 开启事务
        $this->startTrans();

        try {
            foreach ($data['data'] as $key => $value) {
                // 未填写则默认为0
                !empty($value) ?: $value = 0;
                switch ($key) {
                    case 'money':
                    case 'quota':
                    case 'dec_money':
                        $this->setSettingItem($key, $value, 'delivery', 'float');
                        break;

                    case 'number':
                        $this->setSettingItem($key, $value, 'delivery', 'integer');
                        break;

                    case 'money_status':
                    case 'number_status':
                    case 'dec_status':
                        $this->setSettingItem($key, $value, 'delivery', 'status');
                        break;

                    case 'money_exclude':
                    case 'number_exclude':
                    case 'dec_exclude':
                        !empty($value) ?: $value = [];
                        $this->setSettingItem($key, $value, 'delivery', 'int_array', true);
                        break;

                    default:
                        throw new \Exception('键名' . $key . '不在允许范围内');
                }
            }

            $this->commit();
            Cache::tag('setting')->clear();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 设置购物系统
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setShoppingList(array $data): bool
    {
        if (!$this->validateData($data, 'rule')) {
            return false;
        }

        // 开启事务
        $this->startTrans();

        try {
            foreach ($data['data'] as $key => $value) {
                !empty($value) ?: $value = 0;
                switch ($key) {
                    case 'integral':
                    case 'timeout':
                    case 'complete':
                        $this->setSettingItem($key, $value, 'system_shopping', 'integer');
                        break;

                    case 'is_country':
                        $this->setSettingItem($key, $value, 'system_shopping', 'status');
                        break;

                    case 'spacer':
                    case 'source':
                        empty($value) && $value = '';
                        $this->setSettingItem($key, $value, 'system_shopping', 'string');
                        break;

                    case 'invoice':
                    case 'withdraw_fee':
                        $this->setSettingItem($key, $value, 'system_shopping', 'between');
                        break;

                    default:
                        throw new \Exception('键名' . $key . '不在允许范围内');
                }
            }

            $this->commit();
            Cache::tag('setting')->clear();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 设置售后服务
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setServiceList(array $data): bool
    {
        if (!$this->validateData($data, 'rule')) {
            return false;
        }

        // 开启事务
        $this->startTrans();

        try {
            foreach ($data['data'] as $key => $value) {
                switch ($key) {
                    case 'days':
                        !empty($value) ?: $value = 0;
                        $this->setSettingItem($key, $value, 'service', 'integer');
                        break;

                    case 'address':
                    case 'consignee':
                    case 'zipcode':
                    case 'mobile':
                        $this->setSettingItem($key, $value, 'service', 'string');
                        break;

                    default:
                        throw new \Exception('键名' . $key . '不在允许范围内');
                }
            }

            $this->commit();
            Cache::tag('setting')->clear();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 设置系统配置
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setSystemList(array $data): bool
    {
        if (!$this->validateData($data, 'rule')) {
            return false;
        }

        // 开启事务
        $this->startTrans();

        try {
            foreach ($data['data'] as $key => $value) {
                switch ($key) {
                    case 'open_index':
                    case 'open_api':
                    case 'open_api_rest':
                    case 'open_mobile':
                    case 'home_captcha':
                    case 'admin_captcha':
                        !empty($value) ?: $value = 0;
                        $this->setSettingItem($key, $value, 'system_info', 'status');
                        break;

                    case 'close_reason':
                    case 'name':
                    case 'title':
                    case 'keywords':
                    case 'description':
                    case 'logo':
                    case 'square_logo':
                    case 'information':
                    case 'miitbeian':
                    case 'miitbeian_url':
                    case 'miitbeian_ico':
                    case 'beian':
                    case 'beian_url':
                    case 'beian_ico':
                    case 'weixin_url':
                    case 'third_count':
                    case 'qrcode_logo':
                    case 'platform':
                        $this->setSettingItem($key, $value, 'system_info', 'string');
                        break;

                    case 'allow_origin':
                    case 'card_auth':
                        !empty($value) ?: $value = [];
                        $this->setSettingItem($key, $value, 'system_info', 'array', true);
                        break;

                    case 'stats_time':
                        !empty($value) ?: $value = 0;
                        $this->setSettingItem($key, $value, 'system_info', 'integer');
                        break;

                    default:
                        throw new \Exception('键名' . $key . '不在允许范围内');
                }
            }

            $this->commit();
            Cache::tag('setting')->clear();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 检测上传大小是否符合服务器限制
     * @access private
     * @param array $data 外部数据
     * @return bool
     */
    private function checkPostMaxSize(array $data): bool
    {
        if (!array_key_exists('default', $data) && !array_key_exists('file_size', $data)) {
            return true;
        }

        $module = array_key_exists('default', $data)
            ? $data['default']
            : Config::get('careyshop.upload.default');

        if (\oss\careyshop\Upload::MODULE !== $module) {
            return true;
        }

        $fileSize = array_key_exists('file_size', $data)
            ? $data['file_size']
            : Config::get('careyshop.upload.file_size');

        $serverSize = ini_get('upload_max_filesize');
        if (string_to_byte($fileSize) > string_to_byte($serverSize)) {
            return $this->setError(\oss\careyshop\Upload::NAME . ' 上传大小最大仅支持 ' . $serverSize);
        }

        return true;
    }

    /**
     * 检测地址是否不包含前缀(http、https)
     * @access private
     * @param string $key   键名
     * @param string $value 键值
     * @throws
     */
    private function checkUrlPrefix(string $key, string $value)
    {
        $url = [
            'oss'           => '资源获取短地址',
            'careyshop_url' => '资源绑定域名别名',
            'qiniu_url'     => '外链域名',
            'aliyun_url'    => 'Bucket 域名',
        ];

        if (!array_key_exists($key, $url)) {
            return;
        }

        if (preg_match('/^((https|http)?:\/\/)[^\s]+/', $value)) {
            throw new \Exception($url[$key] . '不需要添加地址前缀');
        }
    }

    /**
     * 设置上传配置
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setUploadList(array $data): bool
    {
        if (!$this->validateData($data, 'rule')) {
            return false;
        }

        if (!$this->checkPostMaxSize($data['data'])) {
            return false;
        }

        // 开启事务
        $this->startTrans();

        try {
            foreach ($data['data'] as $key => $value) {
                switch ($key) {
                    case 'default':
                        $this->setSettingItem($key, $value, 'upload', 'default_oss');
                        break;

                    case 'file_size':
                        !empty($value) ?: $value = '0M';
                        $this->setSettingItem($key, $value, 'upload', 'string');
                        break;

                    case 'oss':
                    case 'careyshop_url':
                    case 'qiniu_url':
                    case 'aliyun_url':
                        $this->checkUrlPrefix($key, $value);
                        $this->setSettingItem($key, $value, 'upload', 'string');
                        break;

                    case 'image_ext':
                    case 'file_ext':
                    case 'qiniu_access_key':
                    case 'qiniu_secret_key':
                    case 'qiniu_bucket':
                    case 'aliyun_access_key':
                    case 'aliyun_secret_key':
                    case 'aliyun_bucket':
                    case 'aliyun_endpoint':
                    case 'aliyun_rolearn':
                        $this->setSettingItem($key, $value, 'upload', 'string');
                        break;

                    case 'token_expires':
                        empty($value) && $value = 0;
                        $this->setSettingItem($key, $value, 'upload', 'integer');
                        break;

                    default:
                        throw new \Exception('键名' . $key . '不在允许范围内');
                }
            }

            $this->commit();
            Cache::tag('setting')->clear();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 设置通知配置
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setNoticeList(array $data): bool
    {
        if (!$this->validateData($data, 'rule')) {
            return false;
        }

        // 开启事务
        $this->startTrans();

        try {
            foreach ($data['data'] as $key => $value) {
                switch ($key) {
                    case 'sms':
                    case 'email':
                        $this->setSettingItem($key, $value, 'notice', 'string', true);
                        break;

                    default:
                        throw new \Exception('键名' . $key . '不在允许范围内');
                }
            }

            $this->commit();
            Cache::tag('setting')->clear();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }
}
