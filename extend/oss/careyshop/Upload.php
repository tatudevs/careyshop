<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    本地上传
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/23
 */

namespace oss\careyshop;

const DS = DIRECTORY_SEPARATOR;

use app\common\model\Storage;
use oss\Upload as UploadBase;
use think\facade\Config;
use think\facade\Filesystem;
use think\facade\Route;
use think\File;

class Upload extends UploadBase
{
    /**
     * 模块名称
     * @var string
     */
    const NAME = 'CareyShop(本地上传)';

    /**
     * 模块
     * @var string
     */
    const MODULE = 'careyshop';

    /**
     * 最大上传字节数
     * @var int
     */
    protected static $maxSize;

    /**
     * 最大上传信息
     * @var string
     */
    protected static $maxSizeInfo;

    /**
     * 初始化
     * @access public
     * @return void
     */
    public function initialize()
    {
        $this->setFileMaxSize();
    }

    /**
     * 设置最大可上传大小
     * @access private
     * @return void
     */
    private function setFileMaxSize()
    {
        if (is_null(self::$maxSize)) {
            $serverSize = ini_get('upload_max_filesize');
            $userMaxSize = Config::get('careyshop.upload.file_size');

            if (!empty($userMaxSize)) {
                if (string_to_byte($userMaxSize) < string_to_byte($serverSize)) {
                    self::$maxSizeInfo = $userMaxSize;
                    self::$maxSize = string_to_byte($userMaxSize);
                    return;
                }
            }

            self::$maxSizeInfo = $serverSize;
            self::$maxSize = string_to_byte($serverSize);
        }
    }

    /**
     * 获取上传地址
     * @access public
     * @return array
     */
    public function getUploadUrl()
    {
        $vars = ['method' => 'add.upload.list'];
        $uploadUrl = Route::buildUrl('api/v1/upload', $vars)->domain(true)->build();
        $param = [
            ['name' => 'x:replace', 'type' => 'hidden', 'default' => $this->replace],
            ['name' => 'x:parent_id', 'type' => 'hidden', 'default' => 0],
            ['name' => 'x:filename', 'type' => 'hidden', 'default' => ''],
            ['name' => 'key', 'type' => 'hidden', 'default' => ''],
            ['name' => 'token', 'type' => 'hidden', 'default' => ''],
            ['name' => 'file', 'type' => 'file', 'default' => ''],
        ];

        return ['upload_url' => $uploadUrl, 'module' => self::MODULE, 'param' => $param];
    }

    /**
     * 获取上传Token
     * @access public
     * @param string $replace 替换资源(path)
     * @return array
     */
    public function getToken($replace = '')
    {
        empty($replace) ?: $this->replace = $replace;
        $tokenExpires = Config::get('careyshop.upload.token_expires');

        $response['upload_url'] = $this->getUploadUrl();
        $response['token'] = self::MODULE;
        $response['dir'] = '';

        return ['token' => $response, 'expires' => time() + $tokenExpires,];
    }

    /**
     * 上传资源
     * @access public
     * @return array|false
     */
    public function uploadFiles()
    {
        // 检测请求数据总量不得超过服务器设置值
        $posMaxSize = ini_get('post_max_size');
        if ($this->request->server('CONTENT_LENGTH') > string_to_byte($posMaxSize)) {
            return $this->setError('附件合计总大小不能超过 ' . $posMaxSize);
        }

        // 获取上传资源数据
        $filesData = [];
        $files = $this->request->file();

        // 验证资源
        if (empty($files)) {
            $uploadMaxSize = Config::get('careyshop.upload.file_size');
            if (!string_to_byte($uploadMaxSize)) {
                $uploadMaxSize = ini_get('upload_max_filesize');
            }

            return $this->setError(sprintf('请选择需要上传的附件(单附件大小不能超过 %s)', $uploadMaxSize));
        }

        if ($this->request->has('x:replace', 'param', true) && count($files) > 1) {
            return $this->setError('替换资源只能上传单个文件');
        }

        try {
            $ext = Config::get('careyshop.upload.image_ext') . ',' . Config::get('careyshop.upload.file_ext');
            $rule = sprintf('filesize:%s|fileExt:%s', self::$maxSize, $ext);

            validate(['image' => $rule])->check($files);
        } catch (\Exception $e) {
            return $this->setError($e->getMessage());
        }

        // 实际保存资源
        foreach ($files as $value) {
            if ($this->request->has('x:replace', 'param', true) && count($value) > 1) {
                return $this->setError('替换资源只能上传单个文件');
            }

            if (is_object($value)) {
                $result = $this->saveFile($value);
                if (is_array($result)) {
                    $filesData[] = $result;
                } else {
                    $filesData[] = ['status' => 500, 'message' => $result];
                }
            } else if (is_array($value)) {
                foreach ($value as $item) {
                    $result = $this->saveFile($item);
                    if (is_array($result)) {
                        $filesData[] = $result;
                    } else {
                        $filesData[] = ['status' => 500, 'message' => $result];
                    }
                }
            }
        }

        return $filesData;
    }

    /**
     * 接收第三方推送数据
     * @access public
     * @return false
     */
    public function putUploadData()
    {
        return $this->setError(self::NAME . '模块异常访问!');
    }

    /**
     * 保存资源并写入库
     * @access private
     * @param File $file 上传文件对象
     * @return array|string
     */
    private function saveFile(File $file)
    {
        if (!$file || !$file instanceof File) {
            return '请选择需要上传的附件';
        }

        // 非法附件检测
        $filterMime = [
            'text/x-php',
            'text/html',
            'text/javascript',
            'text/x-python',
            'text/x-java-source',
            'text/x-shellscript',
            'text/x-perl',
            'text/x-sql',
        ];

        if (in_array($file->getMime(), $filterMime)) {
            return '禁止上传非法附件';
        }

        // todo 不再继续,需要边调试边修改
//        $savename = \think\facade\Filesystem::putFileAs( 'topic', $file,'abc.jpg');
//        Filesystem::disk('public')->putFileAs();

        // 保存附件到项目目录
        $filePath = DS . 'uploads' . DS . 'files' . DS;
        if ($this->request->has('x:replace', 'param', true)) {
            $movePath = pathinfo($this->request->param('x:replace'));
            $filePath = $movePath['dirname'] . DS;
            $info = $file->move(ROOT_PATH . 'public' . $filePath, $movePath['basename']);
        } else {
            $saveName = date('Ymd') . DS . guid_v4();
            $info = $file->move(ROOT_PATH . 'public' . $filePath, $saveName);
        }

        if (false === $info) {
            return $file->getError();
        }

        // 判断是否为图片
        [$width, $height] = @getimagesize($info->getPathname());
        $isImage = (int)$width > 0 && (int)$height > 0;

        // 附件相对路径,并统一斜杠为'/'
        $path = APP_PUBLIC_PATH . $filePath . $info->getSaveName();
        $path = str_replace('\\', '/', $path);

        // 自定义附件名
        $filename = $this->request->param('x:filename');

        // 对外访问域名
        $host = Config::get('careyshop_url.value', 'upload');
        if (!$host) {
            $host = $this->request->host();
        }

        // 写入库数据准备
        $data = [
            'parent_id' => (int)$this->request->param('x:parent_id', 0),
            'name'      => !empty($filename) ? $filename : $file->getInfo('name'),
            'mime'      => $file->getInfo('type'),
            'ext'       => mb_strtolower($info->getExtension(), 'utf-8'),
            'size'      => $info->getSize(),
            'pixel'     => $isImage ? ['width' => $width, 'height' => $height] : [],
            'hash'      => $info->hash('sha1'),
            'path'      => $path,
            'url'       => $host . $path . '?type=' . self::MODULE,
            'protocol'  => self::MODULE,
            'type'      => $isImage ? 0 : $this->getFileType($file->getInfo('type')),
        ];

        if ($this->request->has('x:replace', 'param', true)) {
            unset($data['parent_id']);
            $data['url'] .= sprintf('&rand=%s', mt_rand(0, time()));
        }

        $map[] = ['path', '=', $data['path']];
        $map[] = ['protocol', '=', self::MODULE];
        $map[] = ['type', '<>', 2];

        $storageDb = new Storage();
        $result = $storageDb->where($map)->find();

        if (false === $result) {
            return $this->setError($storageDb->getError());
        }

        if (!is_null($result)) {
            // 删除被替换资源的缩略图文件
            if (0 === $result->getAttr('type')) {
                $thumb = ROOT_PATH . 'public' . $data['path'];
                $thumb = str_replace(IS_WIN ? '/' : '\\', DS, $thumb);

                $this->clearThumb($thumb);
            }

            // 替换资源进行更新
            if (false === $result->save($data)) {
                return $this->setError($storageDb->getError());
            }

            $ossResult = $result->setAttr('status', 200)->toArray();
        } else {
            // 插入新记录
            if (false === $storageDb->isUpdate(false)->save($data)) {
                return $this->setError($storageDb->getError());
            }

            $ossResult = $storageDb->setAttr('status', 200)->toArray();
        }

        $ossResult['oss'] = Config::get('oss.value', 'upload');
        return $ossResult;
    }

    /**
     * 根据请求参数组合成hash值
     * @access private
     * @param array  $param 请求参数
     * @param string $path  资源路径
     * @return string|false
     */
    private function getFileSign(array $param, string $path)
    {
        if (!is_file($path)) {
            return false;
        }

        $sign = sha1_file($path);
        foreach ($param as $key => $value) {
            switch ($key) {
                case 'size':
                case 'crop':
                    if (is_array($value) && count($value) <= 2) {
                        $sign .= ($key . implode('', $value));
                    }
                    break;

                case 'resize':
                case 'format':
                case 'quality':
                    if (is_string($value) || is_numeric($value)) {
                        $sign .= ($key . $value);
                    }
                    break;
            }
        }

        return hash('sha1', $sign);
    }

    /**
     * 组合新的URL或PATH
     * @access private
     * @param string $fileName 文件名
     * @param string $suffix   后缀
     * @param array  $fileInfo 原文件信息
     * @param null   $urlArray 外部URL信息
     * @param string $type     新的路径方式
     * @return string
     */
    private function getNewUrl(string $fileName, string $suffix, array $fileInfo, $urlArray = null, $type = 'url')
    {
        if ($type === 'url') {
            $url = $urlArray['scheme'] . '://';
            $url .= $urlArray['host'];
            isset($urlArray['port']) && $url .= ':' . $urlArray['port'];
            $url .= $fileInfo['dirname'];
            $url .= '/' . $fileName;
            $url .= '.' . $suffix;

            if (isset($urlArray['query'])) {
                parse_str($urlArray['query'], $query);
                if (array_key_exists('rand', $query)) {
                    $url .= sprintf('?rand=%s', $query['rand']);
                }
            }
        } else if ($type === 'path') {
            $url = ROOT_PATH . 'public';
            $url .= str_replace(IS_WIN ? '/' : '\\', DS, $fileInfo['dirname']);
            $url .= DS . $fileName;
            $url .= '.' . $suffix;
        } else {
            $url = ROOT_PATH . 'public';
            $url .= str_replace(IS_WIN ? '/' : '\\', DS, $fileInfo['dirname']);
            $url .= DS . $fileInfo['basename'];
        }

        return $url;
    }

    /**
     * 获取缩略大小请求参数
     * @param int    $width     宽度
     * @param int    $height    高度
     * @param mixed  $imageFile 图片文件
     * @param string $resize    缩放方式
     */
    private function getSizeParam(int &$width, int &$height, $imageFile, string $resize)
    {
        if ('pad' === $resize) {
            $width <= 0 && $width = $height;
            $height <= 0 && $height = $width;
        } else if ('proportion' === $resize) {
            [$sWidth, $sHeight] = $imageFile->size();
            $width = ($width / 100) * $sWidth;
            $height = ($width / 100) * $sHeight;
        } else {
            $width <= 0 && $width = $imageFile->width();
            $height <= 0 && $height = $imageFile->height();
        }
    }

    /**
     * 获取资源缩略图实际路径
     * @access public
     * @param array $urlArray 路径结构
     * @return string
     */
    public function getThumbUrl(array $urlArray)
    {
        // 获取自定义后缀,不合法则使用原后缀
        $fileInfo = pathinfo($urlArray['path']);
        $suffix = $fileInfo['extension'];
        $param = $this->request->param();
        $extension = ['jpg', 'png', 'svg', 'bmp', 'tiff', 'webp'];
        $url = $this->getNewUrl($fileInfo['filename'], $fileInfo['extension'], $fileInfo, $urlArray);

        // 非图片资源则直接返回
        if (!in_array(strtolower($fileInfo['extension']), $extension, true)) {
            return $url;
        }

        // 不支持第三方样式,如果存在样式则直接返回
        if (!empty($param['style'])) {
            return $url;
        }

        // 获取源文件位置,并且生成缩略图文件名,验证源文件是否存在
        $source = $this->getNewUrl('', '', $fileInfo, null, null);
        $fileSign = $this->getFileSign($param, $source);

        if (false === $fileSign) {
            return $url . '?error=' . rawurlencode('资源文件不存在');
        }

        // 处理输出格式
        if (!empty($param['suffix'])) {
            if (in_array($param['suffix'], $extension, true)) {
                $suffix = $param['suffix'];
            }
        }

        // 如果缩略图已存在则直接返回(转成缩略图路径)
        $fileInfo['dirname'] .= '/' . $fileInfo['filename'];
        if (is_file($this->getNewUrl($fileSign, $suffix, $fileInfo, null, 'path'))) {
            return $this->getNewUrl($fileSign, $suffix, $fileInfo, $urlArray);
        }

        // 检测尺寸是否正确
        [$sWidth, $sHeight] = @array_pad(isset($param['size']) ? $param['size'] : [], 2, 0);
        [$cWidth, $cHeight] = @array_pad(isset($param['crop']) ? $param['crop'] : [], 2, 0);

        try {
            // 创建图片实例(并且是图片才创建缩略图文件夹)
            $imageFile = Image::open($source);

            $thumb = ROOT_PATH . 'public' . $fileInfo['dirname'];
            $thumb = str_replace(IS_WIN ? '/' : '\\', DS, $thumb);
            !is_dir($thumb) && mkdir($thumb, 0755, true);

            if ($sWidth || $sHeight) {
                // 处理缩放样式
                $resize = isset($param['resize']) ? $param['resize'] : 'scaling';
                $type = 'pad' === $resize ? Image::THUMB_PAD : Image::THUMB_SCALING;

                // 处理缩放尺寸、裁剪尺寸
                foreach ($param as $key => $value) {
                    switch ($key) {
                        case 'size':
                            $this->getSizeParam($sWidth, $sHeight, $imageFile, $resize);
                            $imageFile->thumb($sWidth, $sHeight, $type);
                            break;

                        case 'crop':
                            $cWidth > $imageFile->width() && $cWidth = $imageFile->width();
                            $cHeight > $imageFile->height() && $cHeight = $imageFile->height();
                            $cWidth <= 0 && $cWidth = $imageFile->width();
                            $cHeight <= 0 && $cHeight = $imageFile->height();
                            $x = ($imageFile->width() - $cWidth) / 2;
                            $y = ($imageFile->height() - $cHeight) / 2;
                            $imageFile->crop($cWidth, $cHeight, $x, $y);
                            break;
                    }
                }
            }

            // 处理图片质量
            $quality = 100;
            if (!empty($param['quality'])) {
                $quality = $param['quality'] > 100 ? 100 : $param['quality'];
            }

            // 保存缩略图片
            $savePath = $this->getNewUrl($fileSign, $suffix, $fileInfo, null, 'path');
            $imageFile->save($savePath, $suffix, $quality);
        } catch (\Exception $e) {
            return $url . '?error=' . rawurlencode($e->getMessage());
        }

        return $this->getNewUrl($fileSign, $suffix, $fileInfo, $urlArray);
    }

    /**
     * 批量删除资源
     * @access public
     * @return bool
     */
    public function delFileList()
    {
        foreach ($this->delFileList as $value) {
            $path = ROOT_PATH . 'public' . $value;
            $path = str_replace(IS_WIN ? '/' : '\\', DS, $path);

            $this->clearThumb($path);
            is_file($path) && @unlink($path);
        }

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
        // 去掉后缀名,获得目录路径
        $thumb = mb_substr($path, 0, mb_strripos($path, '.', null, 'utf-8'), 'utf-8');

        if (is_dir($thumb) && $this->checkImg($path)) {
            $matches = glob($thumb . DS . '*');
            is_array($matches) && @array_map('unlink', $matches);
            @rmdir($thumb);
        }
    }

    /**
     * 验证是否为图片
     * @access private
     * @param string $path 路径
     * @return bool
     */
    private function checkImg(string $path)
    {
        $info = @getimagesize($path);
        if (false === $info || (IMAGETYPE_GIF === $info[2] && empty($info['bits']))) {
            return false;
        }

        return true;
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
        $fileInfo = parse_url($url);
        $filePath = ROOT_PATH . 'public' . $fileInfo['path'];
        $filePath = str_replace(IS_WIN ? '/' : '\\', DS, $filePath);

        if (!is_readable($filePath)) {
            header('status: 404 Not Found', true, 404);
        } else {
            // 设置超时时间,避免文件读取过长而导致内容不全
            set_time_limit(0);

            try {
                $sumBuffer = 0;
                $readBuffer = 2048;
                $fp = fopen($filePath, 'rb');
                $size = filesize($filePath);
                $ua = $this->request->header('user-agent');

                $encodedFileName = urlencode($filename);
                $encodedFileName = str_replace('+', '%20', $encodedFileName);
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Accept-Ranges: bytes');
                header('Accept-Length: ' . $size);

                if (preg_match('/Trident/', $ua)) {
                    header('Content-Disposition: attachment; filename="' . $encodedFileName . '"');
                } else if (preg_match('/Firefox/', $ua)) {
                    header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
                } else {
                    header('Content-Disposition: attachment; filename="' . $filename . '"');
                }

                while (!feof($fp) && $sumBuffer < $size) {
                    echo fread($fp, $readBuffer);
                    $sumBuffer += $readBuffer;
                }

                fclose($fp);
            } catch (\Exception $e) {
                header('status: 505 HTTP Version Not Supported', true, 505);
            }

            exit();
        }
    }

    /**
     * 获取资源缩略图信息
     * @access public
     * @param string $url 路径
     * @return array
     */
    public function getThumbInfo(string $url)
    {
        $info = [
            'size'   => 0,
            'width'  => 0,
            'height' => 0,
        ];

        try {
            $fileInfo = parse_url($url);
            $pos = mb_strpos($fileInfo['path'], '/');

            $filePath = ROOT_PATH . 'public' . mb_substr($fileInfo['path'], $pos, null, 'utf-8');
            $result = str_replace(IS_WIN ? '/' : '\\', DS, $filePath);

            if (!file_exists($result)) {
                return $info;
            }

            [$width, $height] = @getimagesize($result);
            if ($width <= 0 || $height <= 0) {
                return $info;
            }

            $info = [
                'size'   => filesize($result),
                'width'  => $width,
                'height' => $height,
            ];
        } catch (\Exception $e) {
            return $info;
        }

        return $info;
    }
}
