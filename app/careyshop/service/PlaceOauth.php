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
use think\facade\Cache;
use think\facade\Db;

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
        $this->model = $config['model'];
        $this->basics = $config['basics'];
        $this->config = $config['config'];

        $this->socialite = new SocialiteManager([$this->model => $this->basics]);
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

        // 获取回调数据
        $oauthUser = $this->getCallback();

        Db::startTrans();
        try {
            // 获取渠道用户
            $userMap[] = ['place_oauth_id', '=', $this->params['place_oauth_id']];
            $userMap[] = ['model', '=', $this->model];
            $userMap[] = ['openid', '=', $oauthUser->getId()];

            $userDB = new \app\careyshop\model\User();
            $placeUserDB = PlaceUser::where($userMap)->find();

            if (is_null($placeUserDB)) {
                // 渠道用户不存在时先创建顾客组账号
                $password = rand_string(8);
                $userData = [
                    'username'         => rand_number(),
                    'password'         => $password,
                    'password_confirm' => $password,
                    'nickname'         => $oauthUser->getNickname(),
                    'head_pic'         => $oauthUser->getAvatar(),
                ];

                if (!$userDB->addUserItem($userData)) {
                    throw new \Exception($userDB->getError());
                }

                // 再创建渠道用户
                // todo 202104091700 待续
            } else {
                // 渠道用户存在需要更新数据
            }

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return $this->setError($e->getMessage());
        }

        // todo 待续
        return $oauthUser;
    }

    /**
     * 获取各个平台的跳转地址
     * @access private
     * @return string
     */
    private function getAuthorize(): string
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

    /**
     * 验证各个平台的回调数据
     * @access private
     * @return mixed
     */
    private function getCallback()
    {
        switch ($this->model) {
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
            ->create($this->model)
            ->userFromCode($this->params['code']);
    }
}
