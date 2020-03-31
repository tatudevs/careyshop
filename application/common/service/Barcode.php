<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    条形码服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/3/31
 */

namespace app\common\service;

use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use think\Url;
use think\Loader;

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
        $data['call_url'] = Url::bUild('/api/v1/barcode', $vars, true, true);

        return $data;
    }

    public function getBarcodeItem($data)
    {
        $validate = Loader::validate('Barcode');
        if (!$validate->check($data)) {
            return $this->setError($validate->getError());
        }

        // 设置默认值
        empty($data['text']) && $data['text'] = base64_decode('Q2FyZXlTaG9w');
        empty($data['type']) && $data['type'] = 'code128';
        empty($data['scale']) && $data['scale'] = 2;
        empty($data['thickness']) && $data['thickness'] = 25;
        empty($data['font_size']) && $data['font_size'] = 10;
        empty($data['generate']) && $data['generate'] = 'image';
        empty($data['suffix']) && $data['suffix'] = 'png';

        switch ($data['type']) {
            case 'codabar':
                $type = BarcodeGenerator::Codabar;
                break;
            case 'code11':
                $type = BarcodeGenerator::Code11;
                break;
            case 'code39':
                $type = BarcodeGenerator::Code39;
                break;
            case 'code39_extended':
                $type = BarcodeGenerator::Code39Extended;
                break;
            case 'ean128':
                $type = BarcodeGenerator::Ean128;
                break;
            case 'gs1128':
                $type = BarcodeGenerator::Gs1128;
                break;
            case 'i25':
                $type = BarcodeGenerator::I25;
                break;
            case 'isbn':
                $type = BarcodeGenerator::Isbn;
                break;
            case 'msi':
                $type = BarcodeGenerator::Msi;
                break;
            case 'postnet':
                $type = BarcodeGenerator::Postnet;
                break;
            case 's25':
                $type = BarcodeGenerator::S25;
                break;
            case 'upca':
                $type = BarcodeGenerator::Upca;
                break;
            default:
                $type = BarcodeGenerator::Code128;
        }

        $barcode = new BarcodeGenerator();
        $barcode->setText($data['text']);
        $barcode->setType($type);
        $barcode->setScale($data['scale']);
        $barcode->setThickness($data['thickness']);
        $barcode->setFontSize($data['font_size']);
        $barcode->setFormat($data['suffix']);

        $code = $barcode->generate();
        if ($data['generate'] == 'base64') {
            return $code;
        }

//        header('Content-type: image/png');
        $code = base64_decode($code);
        print $code;
        exit();
//        return $code;
    }
}
