<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    WeChat 服务层基类
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/19
 */

namespace app\common\wechat\service;

use app\common\wechat\Params;
use app\common\wechat\WeChat;
use think\facade\Config;

class CareyShop
{
    /**
     * 公众号用户缓存标识
     * @var string
     */
    const WECHAT_USER = 'WechatUser';

    /**
     * 公众号黑名单缓存标识
     * @var string
     */
    const WECHAT_BLACK = 'WechatBlack';

    /**
     * 公众号菜单缓存标识
     * $var string
     */
    const WECHAT_MENU = 'WechatMenu';

    /**
     * 控制器版本号
     * @var string
     */
    public $version = 'v1';

    /**
     * 错误信息
     * @var string
     */
    public $error = '';

    /**
     * WeChat 实列
     * @var mixed|null
     */
    private $wechat = null;

    /**
     * 外部请求参数容器
     * @var mixed|null
     */
    public $params = null;

    /**
     * CareyShop constructor.
     * @access public
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->initWechat($params);
    }

    /**
     * 实际创建 WeChat 实列
     * @access public
     * @param array $params 请求参数
     * @return $this
     * @throws
     */
    public function initWechat(array $params)
    {
        if (isset($params['code'])) {
            $this->params = new Params($params);
            $this->wechat = (new WeChat($params['code']))->getApp();
        }

        return $this;
    }

    /**
     * 获取 WeChat 实例化
     * @access public
     * @param string $plate 板块名称
     * @return mixed
     * @throws
     */
    public function getApp(string $plate)
    {
        if (!$this->wechat) {
            throw new \Exception('Wechat未实例化');
        }

        return $this->wechat->$plate;
    }

    /**
     * 设置错误信息
     * @access public
     * @param string $value 错误信息
     * @return false
     */
    public function setError(string $value)
    {
        $this->error = $value;
        return false;
    }

    /*
     * 获取错误信息
     * @access public
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 从请求参数中获取翻页数据
     * @access protected
     * @param int $maxSize
     * @return int[]
     */
    protected function getPageData(int $maxSize = 0)
    {
        $pageNo = $this->params['page_no'];
        $pageSize = $this->params['page_size'];

        $pageNo = empty($pageNo) ? 0 : --$pageNo;
        !empty($pageSize) ?: $pageSize = Config::get('app.list_rows');

        // 限制最大每页数量
        if ($maxSize > 0 && $pageSize > $maxSize) {
            $pageSize = $maxSize;
        }

        return [$pageNo, $pageSize];
    }
}
