<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    七牛云OSS
 *
 * @author      zxm <252404501@qq.com>
 * @date        2018/1/23
 */

namespace oss\qiniu;

use app\careyshop\model\Storage;
use oss\Upload as UploadBase;
use Qiniu\Auth;
use Qiniu\Region;
use Qiniu\Storage\BucketManager;
use Qiniu\Zone;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Route;
use util\Http;

class Upload extends UploadBase
{
    /**
     * 模块名称
     * @var string
     */
    const NAME = '七牛云 KODO';

    /**
     * 模块
     * @var string
     */
    const MODULE = 'qiniu';

    /**
     * 获取回调推送地址
     * @access private
     * @return string
     */
    private function getCallbackUrl(): string
    {
        $vars = ['method' => 'put.upload.data', 'module' => self::MODULE];
        return Route::buildUrl("api/{$this->getVersion()}/upload", $vars)->domain(true)->build();
    }

    /**
     * 获取上传地址
     * @access public
     * @return array|false
     * @throws \throwable
     */
    public function getUploadUrl()
    {
        $zone = Cache::remember('qiniuZone', function () {
            $accessKey = Config::get('careyshop.upload.qiniu_access_key');
            $bucket = Config::get('careyshop.upload.qiniu_bucket');

            return Zone::queryZone($accessKey, $bucket);
        }, 7200);

        if (!$zone instanceof Region) {
            Cache::delete('qiniuZone');
            return $this->setError($zone[1]->message());
        }

        $random = array_rand($zone->cdnUpHosts, 1);
        $uploadUrl = Route::buildUrl('/')->suffix(false)->domain($zone->cdnUpHosts[$random])->build();

        $param = [
            ['name' => 'x:replace', 'type' => 'hidden', 'default' => $this->replace],
            ['name' => 'x:parent_id', 'type' => 'hidden', 'default' => 0],
            ['name' => 'x:filename', 'type' => 'hidden', 'default' => ''],
            ['name' => 'key', 'type' => 'hidden', 'default' => $this->replace],
            ['name' => 'token', 'type' => 'hidden', 'default' => ''],
            ['name' => 'file', 'type' => 'file', 'default' => ''],
        ];

        return ['upload_url' => $uploadUrl, 'module' => self::MODULE, 'param' => $param];
    }

    /**
     * 获取上传Token
     * @access public
     * @param string $replace 替换资源(path)
     * @return array|false
     * @throws \throwable
     */
    public function getToken($replace = '')
    {
        // 初始化Auth状态
        empty($replace) ?: $this->replace = $replace;
        $accessKey = Config::get('careyshop.upload.qiniu_access_key');
        $secretKey = Config::get('careyshop.upload.qiniu_secret_key');
        $bucket = Config::get('careyshop.upload.qiniu_bucket');
        $tokenExpires = Config::get('careyshop.upload.token_expires');

        // 回调参数(别用JSON,处理很麻烦)
        $callbackBody = 'replace=$(x:replace)&parent_id=$(x:parent_id)&filename=$(x:filename)&mime=$(mimeType)&path=$(key)&';
        $callbackBody .= 'size=$(fsize)&name=$(fname)&width=$(imageInfo.width)&height=$(imageInfo.height)&hash=$(etag)';

        // 资源文件前缀
        $key = '';
        $dir = 'uploads/files/' . date('Ymd/');

        if (!empty($this->replace)) {
            $pathInfo = pathinfo($this->replace);
            empty($pathInfo['dirname']) ?: $dir = $pathInfo['dirname'] . '/';
            empty($pathInfo['basename']) ?: $key = $pathInfo['basename'];
        }

        // 组建上传策略
        $policy = [
            // 限定上传附件大小最大值
            'fsizeLimit'       => string_to_byte(Config::get('careyshop.upload.file_size')),
            // 是否以"keyPrefix"为前缀的文件
            'isPrefixalScope'  => empty($this->replace) ? 1 : 0,
            // 回调地址
            'callbackUrl'      => $this->getCallbackUrl(),
            // 回调body信息
            'callbackBody'     => $callbackBody,
            // 回调contentType
            'callbackBodyType' => 'application/x-www-form-urlencoded',
        ];

        $auth = new Auth($accessKey, $secretKey);
        $upToken = $auth->uploadToken($bucket, $dir . $key, $tokenExpires, $policy, true);

        $uploadUrl = $this->getUploadUrl();
        if (false === $uploadUrl) {
            return false;
        }

        $response['upload_url'] = $uploadUrl;
        $response['token'] = $upToken;
        $response['dir'] = $dir;

        return ['token' => $response, 'expires' => time() + $tokenExpires];
    }

    /**
     * 接收第三方推送数据
     * @access public
     * @return array|false
     * @throws
     */
    public function putUploadData()
    {
        // 获取回调body信息
        $callbackBody = file_get_contents('php://input');

        // 回调contentType
        $contentType = 'application/x-www-form-urlencoded';

        // 回调的签名信息,验证该回调是否来自七牛
        $authorization = $this->request->server('HTTP_AUTHORIZATION');

        // 回调地址
        $callbackUrl = $this->getCallbackUrl();

        // 初始化Auth状态
        $accessKey = Config::get('careyshop.upload.qiniu_access_key');
        $secretKey = Config::get('careyshop.upload.qiniu_secret_key');

        $auth = new Auth($accessKey, $secretKey);
        $isQiniuCallback = $auth->verifyCallback($contentType, $authorization, $callbackUrl, $callbackBody);

        if (true !== $isQiniuCallback) {
            return $this->setError(self::NAME . '模块异常访问!');
        }

        // 获取参数
        $params = $this->request->param();

        // 判断是否为图片
        $isImage = (int)$params['width'] > 0 && (int)$params['height'] > 0;

        // 准备写入数据库
        $data = [
            'parent_id' => (int)$params['parent_id'],
            'name'      => !empty($params['filename']) ? $params['filename'] : $params['name'],
            'mime'      => $params['mime'],
            'ext'       => mb_strtolower(pathinfo($params['name'], PATHINFO_EXTENSION), 'utf-8'),
            'size'      => $params['size'],
            'pixel'     => $isImage ? ['width' => (int)$params['width'], 'height' => (int)$params['height']] : [],
            'hash'      => $params['hash'],
            'path'      => $params['path'],
            'url'       => Config::get('careyshop.upload.qiniu_url') . '/' . $params['path'] . '?type=' . self::MODULE,
            'protocol'  => self::MODULE,
            'type'      => $isImage ? 0 : $this->getFileType($params['mime']),
        ];

        if (!empty($params['replace'])) {
            unset($data['parent_id']);
            $data['url'] .= sprintf('&rand=%s', mt_rand(0, time()));
        }

        try {
            !empty($params['replace']) ?: $map[] = ['hash', '=', $data['hash']];
            $map[] = ['path', '=', $data['path']];
            $map[] = ['protocol', '=', self::MODULE];
            $map[] = ['type', '<>', 2];

            $storageDb = new Storage();
            $result = $storageDb->where($map)->find();

            if (!is_null($result)) {
                // 更新已有资源
                $result->save($data);
                $result['status'] = 200;
                $ossResult = $result->toArray();
            } else {
                // 插入新记录
                $storageDb->save($data);
                $storageDb['status'] = 200;
                $ossResult = $storageDb->toArray();
            }
        } catch (\Exception $e) {
            return $this->setError($e->getMessage());
        }

        $ossResult['oss'] = Config::get('careyshop.upload.oss');
        return [$ossResult];
    }

    /**
     * 上传资源
     * @access public
     * @return false
     */
    public function uploadFiles(): bool
    {
        // 直传的意思是客户端直接传附件给OSS,而不再需要应用服务端代为上传,少了转发,速度更快.
        return $this->setError('"' . self::NAME . '"只支持直传附件,详见七牛云开发文档');
    }

    /**
     * 获取缩略大小请求参数
     * @access private
     * @param int    $width  宽度
     * @param int    $height 高度
     * @param string $resize 缩放方式
     * @return string
     */
    private function getSizeParam(int $width, int $height, string $resize): string
    {
        $options = 'thumbnail/';
        if ('proportion' === $resize) {
            $options .= sprintf('!%dp', $width);
        } else {
            $options .= $width != 0 ? (int)$width : '';
            $options .= 'x';
            $options .= $height != 0 ? (int)$height : '';
            $options .= 'pad' !== $resize ? '>' : '';
        }

        $options .= '/';
        return $options;
    }

    /**
     * 获取图片整体大小请求参数
     * @access private
     * @param int $width  宽度
     * @param int $height 高度
     * @return string
     */
    private function getExtentParam(int $width, int $height): string
    {
        $options = '';
        if ($width != 0 || $height != 0) {
            $options = 'extent/';
            $options .= $width != 0 ? (int)$width : '';
            $options .= 'x';
            $options .= $height != 0 ? (int)$height : '';
            $options .= '/background/d2hpdGU=/';
        }

        return $options;
    }

    /**
     * 获取裁剪区域请求参数
     * @access private
     * @param int $width  宽度
     * @param int $height 高度
     * @return string
     */
    private function getCropParam(int $width, int $height): string
    {
        if (!$width && !$height) {
            return '';
        }

        $options = 'gravity/Center/crop/';
        $options .= $width != 0 ? (int)$width : '';
        $options .= 'x';
        $options .= $height != 0 ? (int)$height : '';
        $options .= '/';

        return $options;
    }

    /**
     * 获取资源缩略图实际路径
     * @access public
     * @param array $urlArray  路径结构
     * @param array $styleList 样式集合
     * @return string
     */
    public function getThumbUrl(array $urlArray, array $styleList): string
    {
        // 初始化数据并拼接不带查询条件的URL
        $fileInfo = pathinfo($urlArray['path']);
        $extension = ['jpg', 'png', 'svg', 'gif', 'bmp', 'tiff', 'webp'];

        // 是否带有随机值,用于强制刷新
        $query = [];
        $options = '?imageMogr2/auto-orient/';
        if (isset($urlArray['query'])) {
            parse_str($urlArray['query'], $query);
            if (array_key_exists('rand', $query)) {
                $options = sprintf('?rand=%s&imageMogr2/auto-orient/', $query['rand']);
            }
        }

        // 实际连接
        $port = isset($urlArray['port']) ? ':' . $urlArray['port'] : '';
        $url = sprintf('%s://%s%s%s', $urlArray['scheme'], $urlArray['host'], $port, $urlArray['path']);

        // 带样式则直接返回
        if (!empty($styleList['style'])) {
            $style = mb_substr($styleList['style'], 0, 1, 'utf-8');
            if (in_array($style, ['-', '_', '!', '/'])) {
                $url .= $styleList['style'];
            } else {
                $url .= sprintf('-%s', $styleList['style']);
            }

            if (array_key_exists('rand', $query)) {
                $url .= sprintf('?rand=%s', $query['rand']);
            }

            return $url;
        }

        // 非图片资源则直接返回
        if (!in_array(strtolower($fileInfo['extension']), $extension, true)) {
            return $url;
        }

        // 检测尺寸是否正确
        [$sWidth, $sHeight] = @array_pad(isset($styleList['size']) ? $styleList['size'] : [], 2, 0);
        [$cWidth, $cHeight] = @array_pad(isset($styleList['crop']) ? $styleList['crop'] : [], 2, 0);

        if ($sWidth || $sHeight) {
            // 画布最后的尺寸初始化
            $last = 'size';
            $extent = [0, 0];

            // 处理缩放尺寸、裁剪尺寸
            foreach ($styleList as $key => $value) {
                switch ($key) {
                    case 'size':
                        $last = 'size';
                        $resize = isset($styleList['resize']) ? $styleList['resize'] : '';

                        if ('pad' === $resize) {
                            empty($sWidth) && $sWidth = $sHeight;
                            empty($sHeight) && $sHeight = $sWidth;
                        }

                        $extent = [$sWidth, $sHeight];
                        $options .= $this->getSizeParam($sWidth, $sHeight, $resize);
                        break;

                    case 'crop':
                        $last = 'crop';
                        $extent = [$cWidth, $cHeight];
                        $options .= $this->getCropParam($cWidth, $cHeight);
                        break;
                }
            }

            // 决定图片画布最后的尺寸
            if ($last === 'crop') {
                $extent[0] = $sWidth > $cWidth && $cWidth > 0 ? $cWidth : $sWidth;
                $extent[1] = $sHeight > $cHeight ? $cHeight : $sHeight;
            }

            // 处理画布尺寸
            if (isset($styleList['resize']) && 'pad' === $styleList['resize']) {
                $options .= $this->getExtentParam($extent[0], $extent[1]);
            }
        }

        // 处理图片质量
        if (empty($styleList['quality'])) {
            $options .= 'quality/100!/';
        } else {
            $options .= sprintf('quality/%d!/', (int)$styleList['quality'] > 100 ? 100 : $styleList['quality']);
        }

        // 处理输出格式
        if (!empty($styleList['suffix'])) {
            if (in_array($styleList['suffix'], $extension, true)) {
                $options .= 'format/' . $styleList['suffix'] . '/';
            }
        }

        // 其余参数添加
        $options .= 'interlace/1/';
        return $url . $options;
    }

    /**
     * 批量删除资源
     * @access public
     * @return bool
     */
    public function delFileList(): bool
    {
        if (count($this->delFileList) > 1000) {
            return $this->setError(self::NAME . '批量删除资源不可超过1000个');
        }

        // 初始化Auth状态
        $accessKey = Config::get('careyshop.upload.qiniu_access_key');
        $secretKey = Config::get('careyshop.upload.qiniu_secret_key');
        $bucket = Config::get('careyshop.upload.qiniu_bucket');

        $auth = new Auth($accessKey, $secretKey);
        $config = new \Qiniu\Config();
        $bucketManager = new BucketManager($auth, $config);

        $ops = $bucketManager->buildBatchDelete($bucket, $this->delFileList);
        $bucketManager->batch($ops);

        return true;
    }

    /**
     * 清除缩略图文件夹
     * @access public
     * @param string $path 路径
     * @return void
     */
    public function clearThumb(string $path)
    {
        // 暂不需要
    }

    /**
     * 响应实际下载路径
     * @access public
     * @param string $url      路径
     * @param string $filename 文件名
     * @return void
     */
    public function getDownload(string $url, string $filename)
    {
        $filename = urlencode($filename);
        $filename = str_replace('+', '%20', $filename);

        $url .= '?attname=' . $filename;
        header('Location:' . $url, true, 301);
        exit();
    }

    /**
     * 获取资源缩略图信息
     * @access public
     * @param string $url 路径
     * @return array
     */
    public function getThumbInfo(string $url): array
    {
        $info = [
            'size'   => 0,
            'width'  => 0,
            'height' => 0,
        ];

        try {
            $result = Http::httpGet($url);
            [$width, $height] = @getimagesize('data://image/*;base64,' . base64_encode($result));

            if ($width <= 0 || $height <= 0) {
                return $info;
            }

            $info = [
                'size'   => strlen($result),
                'width'  => $width,
                'height' => $height,
            ];
        } catch (\Exception $e) {
            return $info;
        }

        return $info;
    }
}
