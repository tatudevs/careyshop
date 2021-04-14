<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    WeChat 服务层基类
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/19
 */

namespace app\careyshop\wechat\service;

use app\careyshop\wechat\Params;
use app\careyshop\wechat\WeChat;
use think\facade\Config;
use think\facade\Filesystem;

class CareyShop
{
    use \app\careyshop\concern\Base;
    use \app\careyshop\concern\Error;

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
     * 实列的扩展配置
     * @var mixed|null
     */
    public $expand = null;

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
    public function initWechat(array $params): CareyShop
    {
        // 此处必须isset检测,控制器调用时有二次进入此函数
        if (isset($params['code'])) {
            $this->params = new Params($params);

            $wechat = new WeChat($params['code']);
            $this->wechat = $wechat->getApp();
            $this->expand = $wechat->getExpand();
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
     * 从请求参数中获取翻页数据
     * @access protected
     * @param int $maxSize
     * @return int[]
     */
    protected function getPageData(int $maxSize = 0): array
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

    /**
     * 获取上传文件
     * @access protected
     * @param string $type 上传字段名
     * @return false|string
     */
    protected function getUploadFile(string $type)
    {
        $upType = ['image', 'voice', 'video', 'thumb', 'news'];
        if (!in_array($type, $upType)) {
            return $this->setError(sprintf('参数type只能在 %s 范围内', implode(',', $upType)));
        }

        // 获取上传句柄
        $file = request()->file($type);
        if (empty($file)) {
            return $this->setError('请选择需要上传的素材');
        } else {
            if (is_array($file)) {
                return $this->setError('不允许多素材上传');
            }
        }

        // 根据微信规则所生成的验证规则
        $validate = [
            'image' => ['image' => 'fileSize:10485760|fileExt:bmp,png,jpeg,jpg,gif'],
            'voice' => ['voice' => 'fileSize:2097152|fileExt:mp3,wma,wav,amr'],
            'video' => ['video' => 'fileSize:10485760|fileExt:mp4'],
            'thumb' => ['thumb' => 'fileSize:65536|fileExt:jpg'],
            'news'  => ['news' => 'fileSize:1048576|fileExt:png,jpg'],
        ];

        // 上传文件验证
        $validate = validate($validate[$type], [], false, false);
        if (!$validate->check([$type => $file])) {
            return $this->setError($validate->getError());
        }

        // 完成素材存储
        $driver = Filesystem::disk('public');
        $saveName = $driver->putFile('wechat', $file);

        if (false === $saveName) {
            return $this->setError('上传素材失败');
        }

        return $driver->path($saveName);
    }
}
