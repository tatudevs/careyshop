<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    资源上传服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/9/2
 */

namespace app\common\service;

use app\common\model\Storage;
use app\common\model\StorageStyle;
use think\exception\ValidateException;
use think\facade\Config;
use think\helper\Str;

class Upload extends CareyShop
{
    /**
     * 获取上传模块列表
     * @access public
     * @return array
     */
    public function getUploadModule()
    {
        $moduleList = [
            [
                'name'    => \oss\careyshop\Upload::NAME,
                'module'  => \oss\careyshop\Upload::MODULE,
                'default' => 0,
            ],
            [
                'name'    => \oss\qiniu\Upload::NAME,
                'module'  => \oss\qiniu\Upload::MODULE,
                'default' => 0,
            ],
            [
                'name'    => \oss\aliyun\Upload::NAME,
                'module'  => \oss\aliyun\Upload::MODULE,
                'default' => 0,
            ],
        ];

        $default = Config::get('careyshop.upload.default');
        foreach ($moduleList as &$module) {
            if ($default === $module['module']) {
                $module['default'] = 1;
                break;
            }
        }

        return $moduleList;
    }

    /**
     * 创建资源上传对象
     * @access public
     * @param string $file  目录
     * @param string $model 模块
     * @return object|false
     */
    public function createOssObject(string $file, $model = 'Upload')
    {
        // 转换模块的名称
        $file = Str::lower($file);
        $model = Str::studly($model);

        if (empty($file) || empty($model)) {
            return $this->setError('资源目录或模块不存在');
        }

        $ossObject = '\\oss\\' . $file . '\\' . $model;
        if (class_exists($ossObject)) {
            return new $ossObject;
        }

        return $this->setError($ossObject . '模块不存在');
    }

    /**
     * 获取上传地址
     * @access public
     * @return mixed
     */
    public function getUploadUrl()
    {
        $file = $this->getModuleName();
        if (false === $file) {
            return false;
        }

        $ossObject = $this->createOssObject($file);
        if (false === $ossObject) {
            return false;
        }

        $result = $ossObject->getUploadUrl();
        if (false === $result) {
            return $this->setError($ossObject->getError());
        }

        return $result;
    }

    /**
     * 获取上传Token
     * @access public
     * @return mixed
     */
    public function getUploadToken()
    {
        $file = $this->getModuleName();
        if (false === $file) {
            return false;
        }

        $ossObject = $this->createOssObject($file);
        if (false === $ossObject) {
            return false;
        }

        $result = $ossObject->getToken();
        if (false === $result) {
            return $this->setError($ossObject->getError());
        }

        // 附加可上传后缀及附件大小限制
        $result['image_ext'] = Config::get('careyshop.upload.image_ext');
        $result['file_ext'] = Config::get('careyshop.upload.file_ext');
        $result['file_size'] = Config::get('careyshop.upload.file_size');

        return $result;
    }

    /**
     * 替换上传资源
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function replaceUploadItem(array $data)
    {
        // 规则验证
        try {
            validate(\app\common\validate\Storage::class)->scene('replace')->check($data);
        } catch (ValidateException $e) {
            return $this->setError($e->getMessage());
        }

        // 获取已存在资源数据
        $map[] = ['storage_id', '=', $data['storage_id']];
        $map[] = ['type', '<>', 2];

        $storageDB = new Storage();
        $storageData = $storageDB->field('path,protocol')->where($map)->find();

        if (!$storageData) {
            return $this->setError(is_null($storageData) ? '资源不存在' : $storageDB->getError());
        }

        $ossObject = $this->createOssObject($storageData->getAttr('protocol'));
        if (false === $ossObject) {
            return false;
        }

        $result = $ossObject->getToken($storageData->getAttr('path'));
        if (false === $result) {
            return $this->setError($ossObject->getError());
        }

        // 附加可上传后缀及附件大小限制
        $result['image_ext'] = Config::get('careyshop.upload.image_ext');
        $result['file_ext'] = Config::get('careyshop.upload.file_ext');
        $result['file_size'] = Config::get('careyshop.upload.file_size');

        return $result;
    }

    /**
     * 当参数为空时获取默认上传模块名,否则验证指定模块名并返回
     * @access public
     * @return string|false
     */
    private function getModuleName()
    {
        $module = input('param.module');
        if (empty($module)) {
            return Config::get('careyshop.upload.default');
        }

        $moduleList = array_column($this->getUploadModule(), 'module');
        if (!in_array($module, $moduleList)) {
            return $this->setError('上传模块名 ' . $module . ' 不存在');
        }

        return $module;
    }

    /**
     * 资源上传请求(第三方OSS只能单文件直传方式上传)
     * @access public
     * @return mixed
     */
    public function addUploadList()
    {
        $ossObject = $this->createOssObject('careyshop');
        if (false === $ossObject) {
            return false;
        }

        $result = $ossObject->uploadFiles();
        if (false === $result) {
            return $this->setError($ossObject->getError());
        }

        return $result;
    }

    /**
     * 接收第三方推送数据
     * @access public
     * @return mixed
     */
    public function putUploadData()
    {
        $ossObject = $this->createOssObject(input('param.module', ''));
        if (false === $ossObject) {
            return false;
        }

        $result = $ossObject->putUploadData();
        if (false === $result) {
            return $this->setError($ossObject->getError());
        }

        return $result;
    }

    /**
     * 获取资源缩略图
     * @access public
     * @return mixed
     */
    public function getThumb()
    {
        $url = $this->getThumbUrl();
        if (false === $url) {
            header('Location:' . input('param.url', ''), true, 301);
            exit;
        }

        if (empty($url['url_prefix'])) {
            header('status: 404 Not Found', true, 404);
        } else {
            header('Location:' . $url['url_prefix'], true, 301);
        }

        exit;
    }

    /**
     * 获取资源缩略图实际路径
     * @access public
     * @param bool $getObject 是否返回OSS组件对象
     * @return mixed
     */
    public function getThumbUrl($getObject = false)
    {
        // 补齐协议地址
        $request = request();
        $url = $request->param('url', '');

        $pattern = '/^((http|https)?:\/\/)/i';
        if (!preg_match($pattern, $url)) {
            $url = ($request->isSsl() ? 'https://' : 'http://') . $url;
        }

        // 从URL分析获取对应模型
        $urlArray = parse_url($url);
        if (!isset($urlArray['query'])) {
            return $this->setError('请填写合法的url参数');
        }

        parse_str($urlArray['query'], $items);
        if (!array_key_exists('type', $items)) {
            return $this->setError('type参数值不能为空');
        }

        $pact = array_column($this->getUploadModule(), 'module');
        if (!in_array($items['type'], $pact)) {
            return $this->setError('type协议错误');
        }

        // 是否定义资源样式
        if ($request->has('code', 'param', true)) {
            $style = new StorageStyle();
            $styleResult = $style->getStorageStyleCode(['code' => $request->param('code')]);

            if ($styleResult) {
                foreach ($styleResult as $key => $value) {
                    if ('scale' === $key) {
                        $isMobile = $request->isMobile() ? 'mobile' : 'pc';
                        if (array_key_exists($isMobile, $value)) {
                            $request->get($value[$isMobile]);
                        }

                        continue;
                    }

                    $request->get([$key => $value]);
                }
            }
        }

        $ossObject = $this->createOssObject($items['type']);
        if (false === $ossObject) {
            return false;
        }

        $url = $ossObject->getThumbUrl($urlArray);
        $notPrefix = preg_replace($pattern, '', $url);

        $data = [
            'source'     => $items['type'],
            'url'        => $notPrefix,
            'url_prefix' => strval($url),
        ];

        if (is_bool($getObject) && $getObject) {
            $data['ossObject'] = &$ossObject;
        }

        return $data;
    }

    /**
     * 获取资源缩略图信息
     * @access public
     * @return mixed
     */
    public function getThumbInfo()
    {
        // 协议头
        $request = request();

        $url = $request->param('url');
        if (!$url) {
            return $this->setError('url参数值不能为空');
        }

        $source = $request->param('source');
        if (!$source) {
            return $this->setError('source参数值不能为空');
        }

        if (!in_array($source, array_column($this->getUploadModule(), 'module'))) {
            return $this->setError('source参数值错误');
        }

        $ossObject = $this->createOssObject($source);
        if (false === $ossObject) {
            return false;
        }

        return $ossObject->getThumbInfo($url);
    }

    /**
     * 获取资源下载链接
     * @access public
     * @return void
     */
    public function getDownload()
    {
        // 下载的资源还是需要经过样式处理
        $request = request();
        $url = $this->getThumbUrl(true);

        // 文件不存在,则返回 404 错误提示
        if (empty($url['url_prefix'])) {
            header('status: 404 Not Found', true, 404);
            exit();
        }

        // 不需要强制另存为文件名,也直接返回
        if (!$request->has('filename')) {
            header('Location:' . $url['url_prefix'], true, 301);
            exit();
        }

        // 最终的处理方式由组件决定
        if (isset($url['ossObject'])) {
            $url['ossObject']->getDownload($url['url_prefix'], $request->get('filename'));
        }

        exit();
    }
}
