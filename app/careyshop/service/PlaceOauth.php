<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    OAuth2.0服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/4/6
 */

namespace app\careyshop\service;

use Overtrue\Socialite\SocialiteManager;
use think\facade\Cache;
use util\Aes;

class PlaceOauth extends CareyShop
{
    /**
     * 安全码密钥(可自定义替换)
     * @var string
     */
    const STATE_KEY = 'CAREYSHOP';

    /**
     * 对应模型
     * @var string
     */
    protected string $model = '';

    /**
     * 配置参数
     * @var array
     */
    protected array $basics = [];

    /**
     * 扩展参数
     * @var array
     */
    protected array $config = [];

    /**
     * 第三方扩展库
     * @var object|null
     */
    protected ?object $socialite = null;

    /**
     * PlaceOauth constructor.
     * @access public
     * @param string $model
     * @param array  $basics
     */
    public function __construct(string $model, array $basics)
    {
        $this->model = $model;
        $this->config = $basics['config'] ?? [];
        unset($basics['config']);
        $this->basics = $basics[$model];

        $this->socialite = new SocialiteManager([$model => $this->basics]);
    }

    /**
     * 获取各个平台的跳转地址
     * @access public
     * @return string
     */
    public function getAuthorizeRedirect(): string
    {
        switch ($this->model) {
            case 'wechat':
                return $this->getWeChatRedirect();

            case 'baidu':
                return $this->getBaiduRedirect();

            case 'taobao':
                return $this->getTaobaoRedirect();

            default:
                return $this->getOtherRedirect();
        }
    }

    /**
     * 获取安全码
     * @access private
     * @return array
     */
    private function getState(): array
    {
        $key = md5(rand_number());
        $value = Aes::encrypt($key, self::STATE_KEY, false);
        Cache::set($key, $value, 60 * 5);

        return [$key, $value];
    }

    /**
     * 获取微信跳转地址
     * @access private
     * @return mixed
     */
    private function getWeChatRedirect()
    {
        [$key, $value] = $this->getState();
        $scopes = $this->config['scope'] ?? ['snsapi_userinfo'];

        return $this
            ->socialite
            ->create('wechat')
            ->scopes($scopes)
            ->with(['state_value' => $value])
            ->withState($key)
            ->redirect();
    }

    /**
     * 获取百度跳转地址
     * @access private
     * @return mixed
     */
    private function getBaiduRedirect()
    {
        [$key, $value] = $this->getState();
        $display = $this->config['display'] ?? 'mobile';

        return $this
            ->socialite
            ->create('baidu')
            ->with(['state_value' => $value])
            ->withState($key)
            ->withDisplay($display)
            ->redirect();
    }

    /**
     * 获取淘宝跳转地址
     * @access private
     * @return mixed
     */
    private function getTaobaoRedirect()
    {
        [$key, $value] = $this->getState();
        $view = $this->config['view'] ?? 'wap';

        return $this
            ->socialite
            ->create('taobao')
            ->with(['state_value' => $value])
            ->withState($key)
            ->withView($view)
            ->redirect();
    }

    /**
     * 获取其他平台跳转地址
     * @access private
     * @return string
     */
    private function getOtherRedirect(): string
    {
        [$key, $value] = $this->getState();
        return $this
            ->socialite
            ->create($this->model)
            ->with(['state_value' => $value])
            ->withState($key)
            ->redirect();
    }
}
