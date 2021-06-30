<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    资源管理模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/9/2
 */

namespace app\careyshop\model;

use app\careyshop\service\Upload;
use think\facade\{Cache, Db};

class Storage extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'storage_id';

    /**
     * 是否需要自动写入时间戳
     * @var bool|string
     */
    protected $autoWriteTimestamp = true;

    /**
     * 只读属性
     * @var string[]
     */
    protected $readonly = [
        'storage_id',
        'hash',
        'create_time',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'storage_id' => 'integer',
        'parent_id'  => 'integer',
        'size'       => 'integer',
        'type'       => 'integer',
        'sort'       => 'integer',
        'pixel'      => 'array',
        'is_default' => 'integer',
    ];

    /**
     * 添加一个资源目录
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addStorageDirectoryItem(array $data)
    {
        if (!$this->validateData($data, 'add_directory')) {
            return false;
        }

        // 初始化数据
        $data['type'] = 2;
        $data['protocol'] = '';
        $data['priority'] = 0;
        $field = ['parent_id', 'name', 'type', 'protocol', 'priority', 'sort'];

        if ($this->allowField($field)->save($data)) {
            Cache::tag('StorageDirectory')->clear();
            return $this->hidden(['protocol'])->toArray();
        }

        return false;
    }

    /**
     * 编辑一个资源目录
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setStorageDirectoryItem(array $data)
    {
        if (!$this->validateData($data, 'set_directory', true)) {
            return false;
        }

        $map[] = ['storage_id', '=', $data['storage_id']];
        $map[] = ['type', '=', 2];

        $result = self::update($data, $map, ['name', 'sort']);
        Cache::tag('StorageDirectory')->clear();

        return $result->toArray();
    }

    /**
     * 获取资源目录选择列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getStorageDirectorySelect(array $data)
    {
        if (!$this->validateData($data, 'list_directory')) {
            return false;
        }

        // 获取实际数据
        $result = $this->setDefaultOrder(['storage_id' => 'desc'], ['sort' => 'asc'], true)
            ->cache(true, null, 'StorageDirectory')
            ->field(['storage_id', 'parent_id', 'name', 'cover', 'sort', 'is_default'])
            ->where('type', '=', 2)
            ->withSearch(['page', 'order'], $data)
            ->select();

        return [
            'list'    => $result->toArray(),
            'default' => self::getDefaultStorageId(),
        ];
    }

    /**
     * 将资源目录标设为默认目录
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setStorageDirectoryDefault(array $data): bool
    {
        if (!$this->validateData($data, 'default')) {
            return false;
        }

        $map[] = ['type', '=', 2];
        self::update(['is_default' => 0], $map);

        $map[] = ['storage_id', '=', $data['storage_id']];
        self::update(['is_default' => $data['is_default']], $map);

        Cache::tag('StorageDirectory')->clear();
        return true;
    }

    /**
     * 获取一个资源或资源目录
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getStorageItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        return $this->findOrEmpty($data['storage_id'])->toArray();
    }

    /**
     * 获取资源列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getStorageList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 初始化数据
        $data['storage_id'] ??= 0;

        // 搜索条件
        $map = [];
        empty($data['type']) ?: $map[] = ['type', 'in', $data['type']];

        if (!empty($data['name'])) {
            $map[] = ['name', 'like', '%' . $data['name'] . '%'];
            $map[] = ['storage_id', '<>', (int)$data['storage_id']];
        } else {
            $map[] = ['parent_id', '=', (int)$data['storage_id']];
        }

        // 获取总数量,为空直接返回
        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['storage_id' => 'desc'], ['priority' => 'asc'])
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        return $result;
    }

    /**
     * 根据资源编号获取集合
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getStorageCollection(array $data)
    {
        if (!$this->validateData($data, 'collection')) {
            return false;
        }

        $map[] = ['type', '<>', 2];
        $map[] = ['storage_id', 'in', $data['storage_id']];

        $order = [];
        $result = $this->where($map)->column('*', 'storage_id');

        // 根据传入顺序返回列表
        foreach ($data['storage_id'] as $value) {
            if (array_key_exists($value, $result)) {
                $order[] = $result[$value];
            }
        }

        return $order;
    }

    /**
     * 获取导航数据
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getStorageNavi(array $data)
    {
        if (!$this->validateData($data, 'navi')) {
            return false;
        }

        if (empty($data['storage_id'])) {
            return [];
        }

        $list = self::cache('StorageNavi', null, 'StorageDirectory')
            ->where('type', '=', 2)
            ->column('storage_id,parent_id,name', 'storage_id');

        // 是否返回本级
        $isLayer = is_empty_parm($data['is_layer']) || $data['is_layer'];
        if (!$isLayer && isset($list[$data['storage_id']])) {
            $data['storage_id'] = $list[$data['storage_id']]['parent_id'];
        }

        $result = [];
        while (true) {
            if (!isset($list[$data['storage_id']])) {
                break;
            }

            $result[] = $list[$data['storage_id']];

            if ($list[$data['storage_id']]['parent_id'] <= 0) {
                break;
            }

            $data['storage_id'] = $list[$data['storage_id']]['parent_id'];
        }

        return array_reverse($result);
    }

    /**
     * 重命名一个资源
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function renameStorageItem(array $data)
    {
        if (!$this->validateData($data, 'rename')) {
            return false;
        }

        $map[] = ['storage_id', '=', $data['storage_id']];
        $result = self::update(['name' => $data['name']], $map);
        Cache::tag('StorageDirectory')->clear();

        return $result->toArray();
    }

    /**
     * 将某张图片资源设为目录或视频封面
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function setStorageCover(array $data): bool
    {
        if (!$this->validateData($data, 'cover')) {
            return false;
        }

        $map[] = ['storage_id', '=', $data['storage_id']];
        $map[] = ['type', '=', 0];

        $result = $this->where($map)->find();
        if (is_null($result)) {
            return $this->setError('资源图片不存在');
        }

        $coverMap[] = ['storage_id', '=', $data['parent_id']];
        $coverMap[] = ['type', 'in', [2, 3]];

        self::update(['cover' => $result->getAttr('url')], $coverMap);
        Cache::tag('StorageDirectory')->clear();

        return true;
    }

    /**
     * 清除目录资源的封面
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function clearStorageCover(array $data): bool
    {
        if (!$this->validateData($data, 'clear_cover')) {
            return false;
        }

        $map[] = ['storage_id', '=', $data['storage_id']];
        self::update(['cover' => ''], $map);
        Cache::tag('StorageDirectory')->clear();

        return true;
    }

    /**
     * 批量移动资源到指定目录
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function moveStorageList(array $data)
    {
        if (!$this->validateData($data, 'move')) {
            return false;
        }

        // 开启事务
        $this->startTrans();

        try {
            $data['storage_id'] = array_unique($data['storage_id']);
            $rootId = self::where('storage_id', '=', $data['storage_id'][0])->value('parent_id'); // 原来的父级

            // 不需要任何移动操作
            if ($data['parent_id'] == $rootId) {
                return [];
            }

            // 防止自身移动到自身
            $posNode = array_search($data['parent_id'], $data['storage_id']);
            if (false !== $posNode) {
                unset($data['storage_id'][$posNode]);

                if (empty($data['storage_id'])) {
                    return [];
                }
            }

            if (0 != $data['parent_id']) {
                $parentResult = $this->find($data['parent_id']); // 新的父级
                if (!$parentResult) {
                    throw new \Exception('上级资源目录不存在');
                }

                // 将原来的子级处理为新的父级目录
                if (in_array($parentResult['parent_id'], $data['storage_id'])) {
                    $parentResult->save(['parent_id' => $rootId]);
                }
            }

            $map[] = ['storage_id', 'in', $data['storage_id']];
            if (!$this->where($map)->save(['parent_id' => $data['parent_id']])) {
                throw new \Exception($this->getError());
            }

            $this->commit();
            Cache::tag('StorageDirectory')->clear();

            sort($data['storage_id']);
            return $data['storage_id'];
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 批量删除资源
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function delStorageList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        // 数组转为字符串格式,用于SQL查询条件,为空直接返回
        $delList['storage_id'] = array_unique($data['storage_id']);
        $delList['storage_id'] = implode(',', $delList['storage_id']);

        if (empty($delList['storage_id'])) {
            return true;
        }

        // 获取子节点资源
        $storageId = Db::query('SELECT `getStorageChildrenList`(:storage_id) AS `storage_id`', $delList);

        // 获取所有资源数据(不可使用FIND_IN_SET查询,不走索引,效率极低)
        $result = $this->where('storage_id', 'in', $storageId[0]['storage_id'])->select();

        if ($result->isEmpty()) {
            return true;
        }

        $delDirId = [];
        $result = $result->toArray();
        $ossObjectList = new \StdClass();

        foreach ($result as $value) {
            // 如果是资源目录则加入待删除列表
            if ($value['type'] == 2) {
                $delDirId[] = $value['storage_id'];
                continue;
            }

            if ($value['type'] != 2 && !empty($value['protocol'])) {
                if (!isset($ossObjectList->oss[$value['protocol']])) {
                    $ossObject = new Upload();
                    $ossObjectList->oss[$value['protocol']] = $ossObject->createOssObject($value['protocol']);

                    if (false === $ossObjectList->oss[$value['protocol']]) {
                        return $this->setError($ossObject->getError());
                    }
                }

                $ossObjectList->oss[$value['protocol']]->addDelFile($value['path']);
                $ossObjectList->oss[$value['protocol']]->addDelFileId($value['storage_id']);
            }
        }

        // 开启事务
        $this->startTrans();

        try {
            if (isset($ossObjectList->oss)) {
                foreach ($ossObjectList->oss as $item) {
                    // 删除OSS物理资源
                    if (false === $item->delFileList()) {
                        throw new \Exception($item->getError());
                    }

                    // 删除资源记录
                    $this->where('storage_id', 'in', $item->getDelFileIdList())->delete();
                }
            }

            // 删除资源目录记录
            if (!empty($delDirId)) {
                $this->where('storage_id', 'in', $delDirId)->delete();
            }

            $this->commit();
            Cache::tag('StorageDirectory')->clear();

            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 清除图片资源缓存
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function clearStorageThumb(array $data): bool
    {
        if (!$this->validateData($data, 'thumb')) {
            return false;
        }

        $map[] = ['storage_id', '=', $data['storage_id']];
        $map[] = ['type', '=', 0];

        $result = $this->field('path,protocol,url')->where($map)->find();
        if (is_null($result)) {
            return $this->setError('资源图片不存在');
        }

        $url = parse_url($result['url'], PHP_URL_PATH);
        $newUrl = sprintf('%s?type=%s&rand=%s', $url, $result['protocol'], mt_rand(0, time()));

        // 修改记录
        $result->save(['url' => $newUrl]);

        $path = public_path() . $result['path'];
        $path = str_replace(is_windows() ? '/' : '\\', DIRECTORY_SEPARATOR, $path);

        $ossObject = (new Upload())->createOssObject($result['protocol']);
        $ossObject->clearThumb($path);

        return true;
    }

    /**
     * 获取默认目录的资源编号
     * @access public
     * @return int
     */
    public static function getDefaultStorageId(): int
    {
        $map[] = ['type', '=', 2];
        $map[] = ['is_default', '=', 1];

        return self::cache(true, null, 'StorageDirectory')
            ->where($map)
            ->value('storage_id', 0);
    }
}
