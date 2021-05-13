<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    资源上传服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/9/2
 */

namespace app\careyshop\service;

use app\careyshop\model\{Storage, StorageStyle};
use think\facade\Config;
use think\facade\Request;
use think\helper\Str;

class Upload extends CareyShop
{
    /**
     * 获取上传模块列表
     * @access public
     * @return array[]
     */
    public function getUploadModule(): array
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
     * @return false|object
     */
    public function createOssObject(string $file, string $model = 'Upload')
    {
        // 转换模块的名称
        $file = Str::lower($file);
        $model = Str::studly($model);

        if (empty($file) || empty($model)) {
            return $this->setError('资源目录或模块不存在');
        }

        $ossObject = '\\oss\\' . $file . '\\' . $model;
        if (class_exists($ossObject)) {
            $object = invoke($ossObject);
            $object->setVersion($this->version);

            return $object;
        }

        return $this->setError($ossObject . '模块不存在');
    }

    /**
     * 获取上传地址
     * @access public
     * @return array|false
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
     * @return array|false
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
        if (!$this->validateData($data, 'replace', false, \app\careyshop\validate\Storage::class)) {
            return false;
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
     * @return mixed
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
     * @return array|false
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
     * @param array $data 外部数据
     * @return array|false
     */
    public function putUploadData(array $data)
    {
        $ossObject = $this->createOssObject($data['module'] ?? '');
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
     * @param array $data 外部数据
     * @return void
     */
    public function getThumb(array $data)
    {
        $url = $this->getThumbUrl($data);
        if (false === $url) {
            header('Location:' . $data['url'] ?? '', true, 301);
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
     * @param array $data      外部数据
     * @param bool  $getObject 是否返回OSS组件对象
     * @return array|false
     */
    public function getThumbUrl(array $data, bool $getObject = false)
    {
        // 补齐协议地址
        $url = $data['url'] ?? '';
        $pattern = '/^((http|https)?:\/\/)/i';

        if (!preg_match($pattern, $url)) {
            $url = (Request::isSsl() ? 'https' : 'http') . '://' . $url;
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

        // 样式组合
        $patterns = [];

        // 是否定义资源样式
        if (!is_empty_parm($data['code'])) {
            $style = new StorageStyle();
            $styleResult = $style->getStorageStyleCode(['code' => $data['code']]);

            if ($styleResult) {
                foreach ($styleResult as $key => $value) {
                    // 提取设定资源样式
                    if ('scale' === $key) {
                        $isMobile = Request::isMobile() ? 'mobile' : 'pc';
                        if (array_key_exists($isMobile, $value)) {
                            $patterns = $value[$isMobile];
                        }

                        continue;
                    }

                    // 提取其他样式
                    $patterns[$key] = $value;
                }
            }
        }

        $ossObject = $this->createOssObject($items['type']);
        if (false === $ossObject) {
            return false;
        }

        $patterns = array_merge($patterns, $data);
        $url = $ossObject->getThumbUrl($urlArray, $patterns);
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
     * @param array $data 外部数据
     * @return array|false
     */
    public function getThumbInfo(array $data)
    {
        if (!$data['url']) {
            return $this->setError('url参数值不能为空');
        }

        if (!$data['source']) {
            return $this->setError('source参数值不能为空');
        }

        if (!in_array($data['source'], array_column($this->getUploadModule(), 'module'))) {
            return $this->setError('source参数值错误');
        }

        $ossObject = $this->createOssObject($data['source']);
        if (false === $ossObject) {
            return false;
        }

        return $ossObject->getThumbInfo($data['url']);
    }

    /**
     * 获取资源下载链接
     * @access public
     * @param array $data 外部数据
     * @return void
     */
    public function getDownload(array $data)
    {
        // 下载的资源还是需要经过样式处理
        $url = $this->getThumbUrl($data, true);

        // 文件不存在,则返回 404 错误提示
        if (empty($url['url_prefix'])) {
            header('status: 404 Not Found', true, 404);
            exit();
        }

        // 不需要强制另存为文件名,也直接返回
        if (!isset($data['filename'])) {
            header('Location:' . $url['url_prefix'], true, 301);
            exit();
        }

        // 最终的处理方式由组件决定
        if (isset($url['ossObject'])) {
            $url['ossObject']->getDownload($url['url_prefix'], $data['filename']);
        }

        exit();
    }
}
