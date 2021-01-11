<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    品牌模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/25
 */

namespace app\careyshop\model;

use think\facade\Cache;
use think\helper\Str;
use util\Phonetic;

class Brand extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'brand_id';

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'brand_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'brand_id'          => 'integer',
        'goods_category_id' => 'integer',
        'sort'              => 'integer',
        'status'            => 'integer',
    ];

    /**
     * 添加一个品牌
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addBrandItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        if (!$this->uniqueBrandName($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['brand_id']);

        // 确认用户自定义或系统转换
        if (empty($data['phonetic'])) {
            $data['phonetic'] = Phonetic::encode(Str::substr($data['name'], 0, 1));
            $data['phonetic'] = Str::lower($data['phonetic']);
        }

        if ($this->save($data)) {
            Cache::tag('Brand')->clear();
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个品牌
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setBrandItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        if (!empty($data['name'])) {
            $map[] = ['brand_id', '<>', $data['brand_id']];
            $map[] = ['name', '=', $data['name']];
            $map[] = ['goods_category_id', '=', !empty($data['goods_category_id']) ? $data['goods_category_id'] : 0];

            if (self::checkUnique($map)) {
                return $this->setError('品牌名称已存在');
            }

            // 确认用户自定义或系统转换
            if (empty($data['phonetic'])) {
                $data['phonetic'] = Phonetic::encode(Str::substr($data['name'], 0, 1));
                $data['phonetic'] = Str::lower($data['phonetic']);
            }
        }

        $result = self::update($data, ['brand_id' => $data['brand_id']]);
        Cache::tag('Brand')->clear();

        return $result->toArray();
    }

    /**
     * 批量删除品牌
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delBrandList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        self::destroy($data['brand_id']);
        Cache::tag('Brand')->clear();

        return true;
    }

    /**
     * 批量设置品牌是否显示
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setBrandStatus(array $data): bool
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['brand_id', 'in', $data['brand_id']];
        self::update(['status' => $data['status']], $map);
        Cache::tag('Brand')->clear();

        return true;
    }

    /**
     * 验证品牌名称是否唯一
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function uniqueBrandName(array $data): bool
    {
        if (!$this->validateData($data, 'unique')) {
            return false;
        }

        $map[] = ['name', '=', $data['name']];
        $map[] = ['goods_category_id', '=', !empty($data['goods_category_id']) ? $data['goods_category_id'] : 0];
        !isset($data['exclude_id']) ?: $map[] = ['ads_id', '<>', $data['exclude_id']];

        if (self::checkUnique($map)) {
            return $this->setError('品牌名称已存在');
        }

        return true;
    }

    /**
     * 获取一个品牌
     * @access public
     * @param array $data 外部数据
     * @return array|false|null
     * @throws
     */
    public function getBrandItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $map[] = ['brand_id', '=', $data['brand_id']];
        is_client_admin() ?: $map[] = ['status', '=', 1];

        $result = $this->where($map)->find();
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 获取品牌列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getBrandList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 获取商品分类Id,包括子分类
        $catIdList = [];
        if (isset($data['goods_category_id'])) {
            if (0 == $data['goods_category_id'] || '' == $data['goods_category_id']) {
                $catIdList[] = 0;
            } else {
                $goodsCat = GoodsCategory::getCategoryList($data['goods_category_id'], false, true);
                $catIdList = array_column((array)$goodsCat, 'goods_category_id');
            }
        }

        // 搜索条件
        $map = [];
        empty($data['name']) ?: $map[] = ['b.name', 'like', '%' . $data['name'] . '%'];
        empty($catIdList) ?: $map[] = ['b.goods_category_id', 'in', $catIdList];

        if (is_client_admin()) {
            if (!is_empty_parm($data['status'])) {
                $map[] = ['b.status', '=', $data['status']];
            }
        } else {
            $map[] = ['b.status', '=', 1];
        }

        $result['total_result'] = $this->cache(true, null, 'Brand')->alias('b')->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['brand_id' => 'desc'], ['sort' => 'asc'], true)
            ->cache(true, null, 'Brand')
            ->alias('b')
            ->field('b.*,ifnull(c.name, \'\') category_name,ifnull(c.alias, \'\') category_alias')
            ->join('goods_category c', 'c.status = 1 AND c.goods_category_id = b.goods_category_id', 'left')
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        return $result;
    }

    /**
     * 获取品牌选择列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getBrandSelect(array $data)
    {
        if (!$this->validateData($data, 'select')) {
            return false;
        }

        // 搜索条件
        $map[] = ['b.status', '=', 1];
        !isset($data['goods_category_id']) ?: $map[] = ['b.goods_category_id', 'in', $data['goods_category_id']];

        // 返回字段
        $field = 'b.goods_category_id,b.brand_id,b.name,b.phonetic,b.logo,';
        $field .= 'ifnull(c.name, \'\') category_name,ifnull(c.alias, \'\') category_alias';

        return $this->setDefaultOrder(['brand_id' => 'asc'])
            ->cache(true, null, 'Brand')
            ->alias('b')
            ->field($field)
            ->join('goods_category c', 'c.status = 1 AND c.goods_category_id = b.goods_category_id', 'left')
            ->where($map)
            ->withSearch(['order'], $data)
            ->select()
            ->toArray();
    }

    /**
     * 设置品牌排序
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setBrandSort(array $data): bool
    {
        if (!$this->validateData($data, 'sort')) {
            return false;
        }

        $map[] = ['brand_id', '=', $data['brand_id']];
        self::update(['sort' => $data['sort']], $map);
        Cache::tag('Brand')->clear();

        return true;
    }

    /**
     * 根据编号自动排序
     * @access public
     * @param array $data
     * @return bool
     */
    public function setBrandIndex(array $data): bool
    {
        if (!$this->validateData($data, 'index')) {
            return false;
        }

        foreach ($data['brand_id'] as $key => $value) {
            self::update(['sort' => $key + 1], ['brand_id' => $value]);
        }

        Cache::tag('Brand')->clear();
        return true;
    }
}
