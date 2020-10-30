<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    模板消息服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/29
 */

namespace app\common\wechat\service;

class Template extends CareyShop
{
    /**
     * 获取行业信息列表
     * @access public
     * @return string[][]
     */
    public function getIndustryId()
    {
        return [
            'IT科技'    => [
                1 => '互联网|电子商务',
                2 => 'IT软件与服务',
                3 => 'IT硬件与设备',
                4 => '电子技术',
                5 => '通信与运营商',
                6 => '网络游戏',
            ],
            '金融业'     => [
                7 => '银行',
                8 => '基金理财信托',
                9 => '保险',
            ],
            '餐饮'      => [
                10 => '餐饮',
            ],
            '酒店旅游'    => [
                11 => '酒店',
                12 => '旅游',
            ],
            '运输与仓储'   => [
                13 => '快递',
                14 => '物流',
                15 => '仓储',
            ],
            '教育'      => [
                16 => '培训',
                17 => '院校',
            ],
            '政府与公共事业' => [
                18 => '学术科研',
                19 => '交警',
                20 => '博物馆',
                21 => '公共事业非盈利机构',
            ],
            '医药护理'    => [
                22 => '医药医疗',
                23 => '护理美容',
                24 => '保健与卫生',
            ],
            '交通工具'    => [
                25 => '汽车相关',
                26 => '摩托车相关',
                27 => '火车相关',
                28 => '飞机相关',
            ],
            '房地产'     => [
                29 => '建筑',
                30 => '物业',
            ],
            '消费品'     => [
                31 => '消费品',
            ],
            '商业服务'    => [
                32 => '法律',
                33 => '会展',
                34 => '中介服务',
                35 => '认证',
                36 => '审计',
            ],
            '文体娱乐'    => [
                37 => '传媒',
                38 => '体育',
                39 => '娱乐休闲',
            ],
            '印刷'      => [
                40 => '印刷',
            ],
            '其它'      => [
                41 => '其它',
            ],
        ];
    }

    /**
     * 获取已设置的行业信息
     * @access public
     * @return array|false
     * @throws
     */
    public function getIndustry()
    {
        $result = $this->getApp('template_message')->getIndustry();
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result;
    }

    /**
     * 编辑行业信息
     * @access public
     * @return bool
     * @throws
     */
    public function setIndustry()
    {
        $id1 = $this->params['industry_id1'];
        $id2 = $this->params['industry_id2'];

        $result = $this->getApp('template_message')->setIndustry($id1, $id2);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return true;
    }

    /**
     * 使用公众号的模板库添加至模板列表
     * @access public
     * @return array|false
     * @throws
     */
    public function addTemplateItem()
    {
        $shortId = $this->params['template_id_short'];
        $result = $this->getApp('template_message')->addTemplate($shortId);

        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return ['template_id' => $result['template_id']];
    }

    /**
     * 获取已添加的所有模板列表
     * @access public
     * @return array|false
     * @throws
     */
    public function getTemplateList()
    {
        $result = $this->getApp('template_message')->getPrivateTemplates();
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result['template_list'];
    }

    /**
     * 删除一个指定模板
     * @access public
     * @return bool
     * @throws
     */
    public function delTemplateItem()
    {
        $templateId = $this->params['template_id'];
        $result = $this->getApp('template_message')->deletePrivateTemplate($templateId);

        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return true;
    }
}
