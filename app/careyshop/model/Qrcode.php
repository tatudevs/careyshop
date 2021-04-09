<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    二维码管理模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/14
 */

namespace app\careyshop\model;

use think\facade\Config;

class Qrcode extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'qrcode_id';

    /**
     * 只读属性
     * @var string[]
     */
    protected $readonly = [
        'qrcode_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'qrcode_id' => 'integer',
        'size'      => 'integer',
    ];

    /**
     * 获取一个二维码
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getQrcodeItem($data = [])
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 默认参数初始化
        empty($data['text']) && $data['text'] = pack('H*', 'E59FBAE4BA8E436172657953686F70E59586E59F8EE6A186E69EB6E7B3BBE7BB9F');
        empty($data['size']) && $data['size'] = 75;
        empty($data['suffix']) && $data['suffix'] = 'png';

        if (isset($data['qrcode_id'])) {
            $result = $this->find($data['qrcode_id']);
            if ($result) {
                $data = array_merge($data, $result->toArray());
            }
        }

        // 保留参数
        $data['suffix'] == 'jpg' && $data['suffix'] = 'jpeg';
        empty($data['generate']) && $data['generate'] = 'image';
        empty($data['logo']) && $data['logo'] = Config::get('careyshop.system_info.qrcode_logo');
        $data['logo'] = \app\careyshop\service\Qrcode::getQrcodeLogoPath($data['logo']);

        // 生成二维码
        $qrCode = new \CodeItNow\BarcodeBundle\Utils\QrCode();
        $qrCode
            ->setText($data['text'])
            ->setSize($data['size'])
            ->setPadding(3)
            ->setErrorCorrection('high')
            ->setImageType($data['suffix']);
        $image = $qrCode->getImage();

        ob_start();
        call_user_func('image' . $data['suffix'], $image);
        $imageData = ob_get_contents();
        ob_end_clean();

        // 添加LOGO
        ob_start();
        $qr = imagecreatefromstring($imageData);
        $logo = imagecreatefromstring(file_get_contents(urldecode($data['logo'])));

        $qrWidth = imagesx($qr);
        $logoWidth = imagesx($logo);
        $logoHeight = imagesy($logo);
        $logoQrWidth = $qrWidth / 5;
        $scale = $logoWidth / $logoQrWidth;
        $logoQrHeight = $logoHeight / $scale;
        $fromWidth = ($qrWidth - $logoQrWidth) / 2;
        imagecopyresampled($qr, $logo, $fromWidth, $fromWidth, 0, 0, $logoQrWidth, $logoQrHeight, $logoWidth, $logoHeight);

        call_user_func('image' . $data['suffix'], $qr);
        $content = ob_get_clean();
        imagedestroy($qr);

        if ($data['generate'] == 'base64') {
            return [
                'content_type' => $qrCode->getContentType(),
                'base64'       => base64_encode($content),
            ];
        } else {
            $result = response($content, 200, ['Content-Length' => strlen($content)])
                ->contentType($qrCode->getContentType());

            return [
                'callback_return_type' => 'response',
                'is_callback'          => $result,
            ];
        }
    }

    /**
     * 添加一个二维码
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addQrcodeItem(array $data)
    {
        if (!$this->validateData($data, 'add')) {
            return false;
        }

        // 避免无关字段
        unset($data['qrcode_id']);

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个应用
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setQrcodeItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        $map[] = ['qrcode_id', '=', $data['qrcode_id']];
        $result = self::update($data, $map);

        return $result->toArray();
    }

    /**
     * 获取一个二维码
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getQrcodeConfig(array $data)
    {
        if (!$this->validateData($data, 'config')) {
            return false;
        }

        return $this->findOrEmpty($data['qrcode_id'])->toArray();
    }

    /**
     * 批量删除二维码
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delQrcodeList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        self::destroy($data['qrcode_id']);
        return true;
    }

    /**
     * 获取二维码列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getQrcodeList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['name']) ?: $map[] = ['name', 'like', '%' . $data['name'] . '%'];

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['qrcode_id' => 'desc'])
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        return $result;
    }
}
