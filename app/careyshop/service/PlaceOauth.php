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
use app\careyshop\model\PlaceOauth as PlaceOauthModel;

class PlaceOauth extends CareyShop
{
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
     * 各项配置初始化
     * @access public
     * @param array $config 配置参数
     */
    private function initializeData(array $config)
    {
        $this->model = $config['model'];
        $this->basics = $config['basics'];
        $this->config = $config['config'];

        $this->socialite = new SocialiteManager([$this->model => $this->basics]);
    }

    /**
     * OAuth2.0授权准备
     * @access public
     * @param array $data
     * @return false|string
     */
    public function authorizeOAuth(array $data)
    {
        $oauthDB = new PlaceOauthModel();
        $config = $oauthDB->getOAuthConfig($data);

        if (false === $config) {
            return $oauthDB->setError($oauthDB->getError());
        }

        $this->initializeData($config);
        return $this->getAuthorizeRedirect();
    }

    public function callbackOAuth(array $data)
    {
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
     * 获取微信跳转地址
     * @access private
     * @return mixed
     */
    private function getWeChatRedirect()
    {
        return $this
            ->socialite
            ->create('wechat')
            ->scopes($this->config['scope'] ?? ['snsapi_userinfo'])
            ->redirect();
    }

    /**
     * 获取百度跳转地址
     * @access private
     * @return mixed
     */
    private function getBaiduRedirect()
    {
        return $this
            ->socialite
            ->create('baidu')
            ->withDisplay($this->config['display'] ?? 'mobile')
            ->redirect();
    }

    /**
     * 获取淘宝跳转地址
     * @access private
     * @return mixed
     */
    private function getTaobaoRedirect()
    {
        return $this
            ->socialite
            ->create('taobao')
            ->withView($this->config['view'] ?? 'wap')
            ->redirect();
    }

    /**
     * 获取其他平台跳转地址
     * @access private
     * @return string
     */
    private function getOtherRedirect(): string
    {
        return $this
            ->socialite
            ->create($this->model)
            ->redirect();
    }
}
