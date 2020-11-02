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

const DS = DIRECTORY_SEPARATOR;

class Material extends CareyShop
{
    /**
     * 上传临时素材
     * @access public
     * @return array|false
     * @throws
     */
    public function addMediaItem()
    {
        $type = $this->params['type'];
        if (false === ($path = $this->getUploadFile($type))) {
            return false;
        }

        $wechat = $this->getApp('media');
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

            default:
                $result = false;
        }

        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result;
    }

    /**
     * 获取临时素材
     * @access public
     * @return array|false
     * @throws
     */
    public function getMediaItem()
    {
        $mediaId = $this->params['media_id'];
        $result = $this->getApp('media')->get($mediaId);

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
}
