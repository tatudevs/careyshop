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

use app\careyshop\model\PlaceUser;
use Overtrue\Socialite\SocialiteManager;
use app\careyshop\model\PlaceOauth as PlaceOauthModel;
use think\facade\{Cache, Db};

class PlaceOauth extends CareyShop
{
    /**
     * 对应模块
     * @var string
     */
    protected string $module = '';

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
     * 外部请求参数
     * @var array
     */
    protected array $params = [];

    /**
     * 各项配置初始化
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    private function initializeData(array $data): bool
    {
        $oauthDB = new PlaceOauthModel();
        $config = $oauthDB->getOAuthConfig($data);

        if (false === $config) {
            return $oauthDB->setError($oauthDB->getError());
        }

        $this->params = $data;
        $this->module = $config['module'];
        $this->basics = $config['basics'];
        $this->config = $config['config'];

        $this->socialite = new SocialiteManager([$this->module => $this->basics]);
        return true;
    }

    /**
     * 验证授权是否完成
     * @access public
     * @param array $data 外部数据
     * @return array|mixed
     */
    public function checkOAuth(array $data)
    {
        if (empty($data['guid']) || !Cache::has($data['guid'])) {
            return [];
        }

        $result = Cache::pull($data['guid']);
        return false !== $result ? $result : [];
    }

    /**
     * OAuth2.0授权准备
     * @access public
     * @param array $data 外部数据
     * @return false|string
     */
    public function authorizeOAuth(array $data)
    {
        if ($this->initializeData($data)) {
            return $this->getAuthorize();
        }

        return false;
    }

    /**
     * OAuth2.0回调验证
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function callbackOAuth(array $data)
    {
        if (!$this->initializeData($data)) {
            return false;
        }

        if (empty($this->params['guid'])) {
            return $this->setError('guid不能为空');
        }

        // 获取回调数据
        $oauthUser = $this->getCallback();

        Db::startTrans();
        try {
            // 获取渠道用户
            $userMap[] = ['place_oauth_id', '=', $this->params['place_oauth_id']];
            $userMap[] = ['module', '=', $this->module];
            $userMap[] = ['openid', '=', $oauthUser->getId()];

            $userDB = new \app\careyshop\model\User();
            $placeUserDB = PlaceUser::where($userMap)->findOrEmpty();

            // 渠道用户不存在时先创建顾客组账号
            if ($placeUserDB->isEmpty()) {
                $userData = [
                    'username'         => rand_number(),
                    'password'         => rand_string(8),
                    'nickname'         => $oauthUser->getNickname(),
                    'head_pic'         => $oauthUser->getAvatar(),
                ];

                $userData['password_confirm'] = $userData['password'];
                if (!$userDB->addUserItem($userData)) {
                    throw new \Exception($userDB->getError());
                }
            } else {
                $userDB->setAttr('user_id', $placeUserDB->getAttr('user_id'));
                $userDB->setAttr('username', $placeUserDB->getAttr('username'));
            }

            // 渠道用户数据
            $placeUserData = [
                'user_id'        => $userDB->getAttr('user_id'),
                'username'       => $userDB->getAttr('username'),
                'place_oauth_id' => $this->params['place_oauth_id'],
                'module'         => $this->module,
                'openid'         => $oauthUser->getId(),
                'raw'            => $oauthUser->getRaw(),
                'access_token'   => $oauthUser->getAccessToken(),
                'refresh_token'  => $oauthUser->getRefreshToken(),
                'expires_in'     => $oauthUser->getExpiresIn(),
                'token_response' => $oauthUser->getTokenResponse(),
            ];

            // 插入或更新渠道用户
            if (!$placeUserDB->save($placeUserData)) {
                throw new \Exception($placeUserDB->getError());
            }

            // 模拟登录来获取Token
            $loginData = [
                'username' => $userDB->getAttr('username'),
                'platform' => $this->params['place_oauth_id'],
            ];

            $oauthData = $userDB->loginUser($loginData, true, true);
            if (false === $oauthData) {
                throw new \Exception($userDB->getError());
            }

            // 写入缓存用于验证授权是否完成
            Cache::set($this->params['guid'], $oauthData, 60 * 5);

            Db::commit();
            return $oauthData;
        } catch (\Exception $e) {
            Db::rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 获取各个平台的跳转地址
     * @access private
     * @return string
     */
    private function getAuthorize(): string
    {
        switch ($this->module) {
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
            ->create($this->module)
            ->redirect();
    }

    /**
     * 验证各个平台的回调数据
     * @access private
     * @return mixed
     */
    private function getCallback()
    {
        switch ($this->module) {
            case 'douyin':
                // 预留,如有需要可以像"回调准备"那样实现区分处理
            default:
                return $this->getOtherCallback();
        }
    }

    /**
     * 验证其他平台回调数据
     * @access private
     * @return mixed
     */
    private function getOtherCallback()
    {
        return $this
            ->socialite
            ->create($this->module)
            ->userFromCode($this->params['code']);
    }
}
