<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    条形码服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/3
 */

namespace app\common\service;

use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use think\exception\ValidateException;
use think\facade\Route;

class Barcode extends CareyShop
{
    /**
     * 获取条形码调用地址
     * @access public
     * @return array
     */
    public function getBarcodeCallurl()
    {
        $vars = ['method' => 'get.barcode.item'];
        $data['call_url'] = Route::buildUrl("api/{$this->version}/barcode", $vars)->domain(true)->build();

        return $data;
    }

    /**
     * 获取一个条形码
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getBarcodeItem($data = [])
    {
        // 规则验证
        try {
            validate(\app\common\validate\Barcode::class)->check($data);
        } catch (ValidateException $e) {
            return $this->setError($e->getMessage());
        }

        // 设置默认值
        empty($data['text']) && $data['text'] = pack('H*', '436172657953686F70');
        empty($data['type']) && $data['type'] = 'code128';
        empty($data['scale']) && $data['scale'] = 1;
        empty($data['thickness']) && $data['thickness'] = 40;
        empty($data['font_size']) && $data['font_size'] = 10;
        empty($data['generate']) && $data['generate'] = 'image';
        empty($data['suffix']) && $data['suffix'] = 'png';
        $data['suffix'] == 'jpg' && $data['suffix'] = 'jpeg';

        switch ($data['type']) {
            case 'codabar':
                $data['type'] = BarcodeGenerator::Codabar;
                break;
            case 'code11':
                $data['type'] = BarcodeGenerator::Code11;
                break;
            case 'code39':
                $data['type'] = BarcodeGenerator::Code39;
                break;
            case 'code39_extended':
                $data['type'] = BarcodeGenerator::Code39Extended;
                break;
            case 'ean128':
                $data['type'] = BarcodeGenerator::Ean128;
                break;
            case 'gs1128':
                $data['type'] = BarcodeGenerator::Gs1128;
                break;
            case 'i25':
                $data['type'] = BarcodeGenerator::I25;
                break;
            case 'isbn':
                $data['type'] = BarcodeGenerator::Isbn;
                break;
            case 'msi':
                $data['type'] = BarcodeGenerator::Msi;
                break;
            case 'postnet':
                $data['type'] = BarcodeGenerator::Postnet;
                break;
            case 's25':
                $data['type'] = BarcodeGenerator::S25;
                break;
            case 'upca':
                $data['type'] = BarcodeGenerator::Upca;
                break;
            default:
                $data['type'] = BarcodeGenerator::Code128;
        }

        $barcode = new BarcodeGenerator();
        $barcode->setText($data['text']);
        $barcode->setType($data['type']);
        $barcode->setScale($data['scale']);
        $barcode->setThickness($data['thickness']);
        $barcode->setFontSize($data['font_size']);
        $barcode->setFormat($data['suffix']);

        $content = $barcode->generate();
        if ($data['generate'] == 'base64') {
            return [
                'content_type' => 'image/' . $data['suffix'],
                'base64'       => $content,
            ];
        } else {
            $content = base64_decode($content);
            $result = response($content, 200, ['Content-Length' => strlen($content)])
                ->contentType('image/' . $data['suffix']);

            return [
                'callback_return_type' => 'response',
                'is_callback'          => $result,
            ];
        }
    }
}
