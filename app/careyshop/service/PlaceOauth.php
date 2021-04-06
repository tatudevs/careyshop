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

class PlaceOauth extends CareyShop
{
    /**
     * @var string 对应模型
     */
    protected string $model = '';

    /**
     * @var array 配置参数
     */
    protected array $basics = [];

    /**
     * @var array 扩展参数
     */
    protected array $config = [];

    /**
     * @var object|null
     */
    protected ?object $socialite = null;

    /**
     * @access public
     * PlaceOauth constructor.
     * @param string $model
     * @param array  $basics
     */
    public function __construct(string $model, array $basics)
    {
        $this->model = $model;
        $this->config = $basics['config'] ?? [];
        unset($basics['config']);
        $this->basics = $basics[$model];

        $this->socialite = new SocialiteManager($this->basics);
    }

    public function getAuthorizeRedirect()
    {
        switch ($this->model) {
            case 'wechat':
                return $this->getWeChatRedirect();
        }

        return $this->setError('对应的模型不存在');
    }

    private function getWeChatRedirect()
    {
        print_r($this->config);exit();
    }
}
