<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    素材管理服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/30
 */

namespace app\common\wechat\service;

use EasyWeChat\Kernel\Messages\Article;

const DS = DIRECTORY_SEPARATOR;

class Material extends CareyShop
{
    /**
     * 上传素材
     * @access private
     * @param string $module 上传模块
     * @return array|false
     * @throws
     */
    private function uploadFile(string $module)
    {
        $type = $this->params['type'];
        if (false === ($path = $this->getUploadFile($type))) {
            return false;
        }

        $wechat = $this->getApp($module);
        switch ($type) {
            case 'image':
                $result = $wechat->uploadImage($path);
                break;

            case 'voice':
                $result = $wechat->uploadVoice($path);
                break;

            case 'video':
                $title = $this->params['title'];
                $description = $this->params['description'];
                $result = $wechat->uploadVideo($path, $title, $description);
                break;

            case 'thumb':
                $result = $wechat->uploadThumb($path);
                break;

            case 'news':
                $result = $wechat->uploadArticleImage($path);
                break;

            default:
                $result = false;
        }

        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result;
    }

    /**
     * 获取素材
     * @access private
     * @param string $module 下载模块
     * @return array|false
     * @throws
     */
    private function downloadFile(string $module)
    {
        $mediaId = $this->params['media_id'];
        $result = $this->getApp($module)->get($mediaId);

        if ($result instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $dir = 'uploads' . DS . 'wechat' . DS . date('Ymd') . DS;
            $saveName = $result->save(public_path() . $dir);
            $url = url($dir . $saveName, [], false, true)->build();

            return [
                'path' => $dir . $saveName,
                'url'  => $url,
            ];
        } else if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result;
    }

    /**
     * 上传临时素材
     * @access public
     * @return array|false
     */
    public function addMediaItem()
    {
        return $this->uploadFile('media');
    }

    /**
     * 获取临时素材
     * @access public
     * @return array|false
     */
    public function getMediaItem()
    {
        return $this->downloadFile('media');
    }

    /**
     * 上传永久素材
     * @access public
     * @return array|false
     */
    public function addMaterialItem()
    {
        return $this->uploadFile('material');
    }

    /**
     * 获取永久素材
     * @access public
     * @return array|false
     */
    public function getMaterialItem()
    {
        return $this->downloadFile('material');
    }

    /**
     * 获取图文对象
     * @access private
     * @param array $article 图文数据
     * @return Article
     */
    private function getArticle(array $article): Article
    {
        $result = [];
        $maps = [
            'title'                 => '',
            'thumb_media_id'        => '',
            'author'                => '',
            'digest'                => '',
            'show_cover'            => '',
            'show_cover_pic'        => 0,
            'content'               => '',
            'content_source_url'    => '',
            'need_open_comment'     => 0,
            'only_fans_can_comment' => 0,
        ];

        foreach ($maps as $key => $value) {
            $result[$key] = isset($article[$key]) ? $article[$key] : $value;
        }

        return new Article($result);
    }

    /**
     * 上传永久图文素材
     * @access public
     * @return array|false
     * @throws
     */
    public function addMaterialNews()
    {
        $article = null;
        $news = $this->params['news'] ?? [];

        if (count($news) == count($news, 1)) {
            $article = $this->getArticle($news);
        } else {
            foreach ($news as $item) {
                $article[] = $this->getArticle($item);
            }
        }

        $result = $this->getApp('material')->uploadArticle($article);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result;
    }

    /**
     * 编辑永久图文素材
     * @access public
     * @return bool
     * @throws
     */
    public function setMaterialNews(): bool
    {
        $index = $this->params['index'] ?? 0;
        $mediaId = $this->params['media_id'];
        $article = $this->getArticle($this->params['news'] ?? []);

        $result = $this->getApp('material')->updateArticle($mediaId, $article, $index);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return true;
    }

    /**
     * 上传永久图文消息图片
     * @access public
     * @return array|false
     */
    public function addMaterialImage()
    {
        return $this->uploadFile('material');
    }

    /**
     * 获取永久素材列表
     * @access public
     * @return array|false
     * @throws
     */
    public function getMaterialList()
    {
        // 数据准备
        $type = $this->params['type'];
        $typeMap = ['image', 'voice', 'video', 'news'];
        [$pageNo, $pageSize] = $this->getPageData(20);

        if (!in_array($type, $typeMap)) {
            return $this->setError(sprintf('参数type只能在 %s 范围内', implode(',', $typeMap)));
        }

        $result = $this->getApp('material')->list($type, $pageNo * $pageSize, $pageSize);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result;
    }

    /**
     * 获取永久素材的总数
     * @access public
     * @return array|false
     * @throws
     */
    public function getMaterialCount()
    {
        $result = $this->getApp('material')->stats();
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result;
    }

    /**
     * 删除一个永久素材
     * @access public
     * @return bool
     * @throws
     */
    public function delMaterialItem(): bool
    {
        $result = $this->getApp('material')->delete($this->params['media_id']);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return true;
    }

    /**
     * 发送图文素材的预览
     * @access public
     * @return bool
     * @throws
     */
    public function sendMaterialView(): bool
    {
        $openId = $this->params['open_id'];
        $wxname = $this->params['wxname'];
        $mediaId = $this->params['media_id'];

        $result = null;
        $wechat = $this->getApp('broadcasting');

        if ($wxname) {
            $result = $wechat->previewNewsByName($mediaId, $wxname);
        } else if ($openId) {
            $result = $wechat->previewNews($mediaId, $openId);
        } else {
            return $this->setError('参数"wxname"或"open_id"其中之一必填');
        }

        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return true;
    }
}
