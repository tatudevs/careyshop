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
use think\facade\Filesystem;
use think\facade\Validate;

class CareyShop
{
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

    protected function getUploadFile()
    {
        // 获取上传句柄
        $files = request()->file($type);
        if (empty($files)) {
            return $this->setError('请选择需要上传的素材');
        } else {
            is_array($files) ?: $files = [$files];
        }

        // 验证规则
        $validate = [
            'image' => ['image' => 'filesize:10240|fileExt:bmp,png,jpeg,jpg,gif'],
            'voice' => ['voice' => 'filesize:2048|fileExt:mp3,wma,wav,amr'],
            'video' => ['video' => 'filesize:10240|fileExt:mp4'],
            'thumb' => ['thumb' => 'filesize:64|fileExt:jpg'],
        ];

        // 上传文件验证
//        foreach ($files as $file) {
//            if (!Validate::check([$type => $file], $validate[$type])) {
//                print_r(Validate::getError());exit();
////                return $this->setError();
//            }
//        }

//        $paths = [];
//        $driver = Filesystem::disk('public');
//
//        foreach ($files as $file) {
//            $saveName = $driver->putFile('wechat', $file);
//            if (false === $saveName) {
//                return $this->setError('上传素材失败');
//            }
//
//            $paths[] = $driver->path($saveName);
//        }
//
//        return $paths;
    }
}
