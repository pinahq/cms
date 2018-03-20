<?php

namespace Pina\Modules\Import;

use Pina\Arr;
use Pina\Log;
use Pina\Modules\CMS\TagGateway;
use Pina\Modules\CMS\Resource;
use Pina\Modules\CMS\ResourceGateway;
use Pina\Modules\CMS\ResourceTypeGateway;
use Pina\Modules\CMS\ResourceTagGateway;
use Pina\Modules\CMS\ResourceTextGateway;
use Pina\Modules\CMS\ResourceImageGateway;
use Pina\Modules\Images\ImageGateway;
use Pina\Modules\Images\ImageDomain;
use Pina\Modules\Cart\OfferGateway;
use Pina\Modules\Cart\OfferTagGateway;

class Import
{

    protected $importId = 0;
    protected $fields = array();
    protected $missingStatus = '';
    protected $importKeys = [];
    protected $resourceKeyFields = ['resource_external_id' => 'resource_external_id']; //array('tag_type' => ['Бренд', 'Артикул', 'Цвет']);
    protected $productResourceTypeId = 0;
    protected $schema = [];
    protected $lineTags = [];
    protected $lineTagIds = [];
    protected $lineResourceId = 0;
    protected $lineResourceCreated = false;
    protected $updateAllowed = true;

    public function __construct($importId)
    {
        $this->importId = $importId;

        /*
         * Проверить возможен ли импорт:
         * - не дублируются ли ключевые поля в схеме в разных колонках
         * - не пустое ли ключевое поле
         */
        //Загружаем параметры импорта
        $this->loadImportTaskParams();
    }

    public function import()
    {
        //Выставляем начальный статус задачи
        $this->updateTaskStatus('import');

        //Выбираем записи без ошибок
        $gw = ImportPreviewGateway::instance()
            ->whereBy('import_id', $this->importId)
            ->whereWithoutErrors();

        $step = 100;
        $importCounter = 0;
        $i = 0;

        while ($items = $gw->limit($i, $step)->get()) {
            foreach ($items as $item) {
                $line = json_decode($item['preview'], true);
                try {
                    $this->importLine($line);
                } catch (\Exception $e) {
                    die($e->getMessage());
                    #$this->insertImportStats($productId, $variantId, 'skipped', $message);
                    return;
                }
                $importCounter ++;
            }

            $i += $step;
        }

        //Выставляем финальный статус задачи
        $this->updateTaskStatus('done');

        //Завершаем задачу
        $this->finalize();

        return $importCounter;
    }

    //Загружает параметры задания
    private function loadImportTaskParams()
    {
        $import = ImportGateway::instance()->find($this->importId);

        if (!$import) {
            throw new \Exception("Import task not found " . $this->importId);
        }

        if ($import['status'] != 'confirm') {
            #throw new \Exception('import state is wrong (' . $import['status'] . ')');
        }

        $this->schema = json_decode($import['schema'], true);
        $this->importKeys = json_decode($import['keys'], true);

        if (isset($this->importKeys['resource']) && is_array($this->importKeys['resource'])) {
            $this->resourceKeyFields = [];
            $keyInfo = Schema::schemaKeyInfo();

            foreach ($this->importKeys['resource'] as $key) {
                $keyField = $this->schema[$key];

                if (isset($keyInfo[$keyField]) && $keyInfo[$keyField] == 'resource') {
                    $this->resourceKeyFields[$keyField] = $keyField;
                    continue;
                }

                if (strncmp($keyField, 'tag ', 4) === 0) {
                    $tagType = trim(substr($keyField, 4));
                    if (!isset($this->resourceKeyFields['tag_type'])) {
                        $this->resourceKeyFields['tag_type'] = [];
                    }
                    $this->resourceKeyFields['tag_type'][] = $tagType;
                }
            }
        }

        $this->productResourceTypeId = ResourceTypeGateway::instance()->whereBy('type', 'products')->id();
        $this->categoryResourceTypeId = ResourceTypeGateway::instance()->whereBy('type', 'categories')->id();
        $this->resourceTreeTypes = ResourceTypeGateway::instance()->whereBy('tree', 'Y')->column('title');
        $this->resourceTypes = ResourceTypeGateway::instance()->column('title');
    }

    //Обновляет статус задания
    private function updateTaskStatus($status)
    {
        if (empty($status)) {
            return;
        }

        ImportGateway::instance()->whereId($this->importId)->update(['status' => $status]);
    }

    protected function importLine($line)
    {
        $this->lineResourceId = null;
        $this->lineResourceCreated = null;

        $this->getTags($line);
        $this->getResource($line);
        $this->putResourceText($line);
        $this->getParent($line);
        $this->getImage($line);
    }

    private function getTags($line)
    {
        $this->lineTags = [];
        $this->lineTagIds = [];

        foreach ($this->schema as $k => $item) {
            if (!empty($line[$k]) && strncmp($item, 'tag ', 4) === 0) {
                $tagType = trim(substr($item, 4));
                $tagTitle = $line[$k];

                $tagId = TagGateway::instance()->getIdOrAdd($tagType . ': ' . $tagTitle);
                $this->lineTags[$tagType][] = $tagId;
                $this->lineTagIds[] = $tagId;
            }
        }
    }

    private function getImage($line)
    {
        if (empty($this->lineResourceId)) {
            return false;
        }

        if (!$this->updateAllowed && !$this->lineResourceCreated) {
            return false;
        }

        $first = true;
        foreach ($this->schema as $key => $item) {
            if ($item === 'image' && !empty($line[$key])) {

                $imageId = 0;

                $parsed = parse_url($line[$key]);
                if (!empty($parsed['path'])) {
                    $filename = basename($parsed['path']);
                    $generatedUrl = ImageDomain::getFileUrl($filename);
                    if (trim($generatedUrl) == trim($line[$key])) {
                        $imageId = ImageGateway::instance()->whereBy('filename', $filename)->id();
                    }
                }

                if (empty($imageId)) {
                    $imageId = ImageGateway::instance()->whereBy('original_url', $line[$key])->id();
                }

                if (!$imageId) {
                    $filename = $this->lineResourceId . '-' . basename($line[$key]);
                    $imageId = ImageDomain::saveUrl($line[$key], $filename);
                }

                $isAssigned = ResourceImageGateway::instance()->whereBy('image_id', $imageId)->whereBy('resource_id', $this->lineResourceId)->exists();

                if ($imageId && !$isAssigned) {
                    ResourceImageGateway::instance()->put(array(
                        'resource_id' => $this->lineResourceId,
                        'image_id' => $imageId
                    ));
                }

                if ($imageId && $first) {
                    $first = false;
                    ResourceGateway::instance()->whereId($this->lineResourceId)->update(array('image_id' => $imageId));
                }
            }

            if ($item === 'image_url' && !empty($line[$key])) {

                $imageId = 0;
                if (empty($imageId)) {
                    $imageId = ImageGateway::instance()->whereBy('original_url', $line[$key])->id();
                }

                if (empty($imageId)) {
                    $imageId = ImageGateway::instance()->whereBy('url', $line[$key])->id();
                }

                if (!$imageId) {
                    $imageId = ImageGateway::instance()->insertGetId(['url' => $line[$key], 'original_url' => $line[$key]]);
                }

                $isAssigned = ResourceImageGateway::instance()->whereBy('image_id', $imageId)->whereBy('resource_id', $this->lineResourceId)->exists();

                if ($imageId && !$isAssigned) {
                    ResourceImageGateway::instance()->put(array(
                        'resource_id' => $this->lineResourceId,
                        'image_id' => $imageId
                    ));
                }

                if ($imageId && $first) {
                    $first = false;
                    ResourceGateway::instance()->whereId($this->lineResourceId)->update(array('image_id' => $imageId));
                }
            }
        }

        return true;
    }

    private function getParent($line)
    {
        if (empty($this->lineResourceId)) {
            return false;
        }

        if (!$this->updateAllowed && !$this->lineResourceCreated) {
            return false;
        }

        foreach ($this->schema as $k => $item) {
            if ($item == 'resource_parent' && !empty($line[$k])) {

                $resourceId = Resource::fillTreeWithPath(
                        str_replace('//', '/', $line[$k]), $this->categoryResourceTypeId
                );

                if (!empty($resourceId)) {
                    ResourceGateway::instance()->whereId($this->lineResourceId)->update(array('parent_id' => $resourceId));
                }
            }

            if ($item == 'resource_tag' && !empty($line[$k])) {
                $tag = [];
                foreach ($this->resourceTreeTypes as $treeType) {
                    $tag[] = $treeType . ': ' . str_replace('//', '/', $line[$k]);
                }
                $tagId = TagGateway::instance()->whereBy('tag', $tag)->whereNotBy('resource_id', 0)->id();

                if (!empty($tagId)) {
                    ResourceTagGateway::instance()->insertIgnore(['resource_id' => $this->lineResourceId, 'tag_id' => $tagId]);
                }
            }
        }
    }

    protected function extract($prefix, $line)
    {
        $prefixLength = strlen($prefix);

        $newArray = [];
        foreach ($this->schema as $k => $item) {
            if (strncmp($item, $prefix, $prefixLength) === 0) {
                $newKey = trim(substr($item, $prefixLength));
                $newArray[$newKey] = $line[$k];
            }
        }

        return $newArray;
    }

    private function getResource($line)
    {
        $data = $this->extract('resource.', $line);

        $this->lineResourceId = $this->getId(
            $line, ResourceGateway::instance()->whereBy('resource_type_id', $this->productResourceTypeId), $this->resourceKeyFields, $this->lineTags
        );

        if (empty($this->lineResourceId)) {
            $data['resource_type_id'] = $this->productResourceTypeId;
/*
            $sku = '';
            foreach ($this->schema as $k => $v) {
                if ($v == 'tag Обозначение производителя') {
                    $sku = isset($line[$k]) ? $line[$k] : '';
                }
            }

            $data['resource'] = Resource::generateUniqueResource($sku . '-' . $data['title']);
*/
            $this->lineResourceId = ResourceGateway::instance()->insertGetId($data);
            $this->lineResourceCreated = true;
        } else if ($this->updateAllowed) {
            ResourceGateway::instance()->whereId($this->lineResourceId)->update($data);
            $this->lineResourceCreated = false;
        } else {
            return false;
        }

        if (!empty($this->importId)) {
            ImportResourceGateway::instance()->insertIgnore([
                'import_id' => $this->importId,
                'resource_id' => $this->lineResourceId,
                'status' => $this->lineResourceCreated ? 'added' : 'updated',
            ]);
        }

        //TODO: ->context('resource_id', $this->lineResourceId)
        $links = array();
        foreach ($this->lineTagIds as $tagId) {
            $links[] = array('resource_id' => $this->lineResourceId, 'tag_id' => $tagId);
        }
        ResourceTagGateway::instance()->insertIgnore($links);

        //TODO: ->context('import_id', $this->importId)->context('resource_id', $this->lineResourceId)
        if (!empty($this->importId)) {
            $links = array();
            foreach ($this->lineTagIds as $tagId) {
                $links[] = array('import_id' => $this->importId, 'resource_id' => $this->lineResourceId, 'tag_id' => $tagId);
            }
            ImportResourceTagGateway::instance()->insertIgnore($links);
        }
    }

    private function putResourceText($line)
    {
        if (empty($this->lineResourceId)) {
            return false;
        }

        if (!$this->updateAllowed && !$this->lineResourceCreated) {
            return false;
        }

        $data = $this->extract('resource_text.', $line);
        $data['resource_id'] = $this->lineResourceId;

        if (!isset($data['text'])) {
            return;
        }

        $gw = ResourceTextGateway::instance()->whereBy('resource_id', $this->lineResourceId);

        if ($gw->exists()) {
            if (!empty($this->fields) && is_array($this->fields)) {
                $gw->update($data, $this->fields);
            } else {
                $gw->update($data);
            }
        } else {
            $gw->insert($data);
        }
    }

    protected function getId($line, $gw, $keySchema, $tags)
    {
        $data = $this->combine($line);

        $tagIds = [];
        foreach ($keySchema as $keyType => $keyFields) {
            if ($keyType === 'tag_type') {
                if (is_array($keyFields)) {
                    foreach ($keyFields as $keyTagType) {
                        if (!isset($tags[$keyTagType]) || !is_array($tags[$keyTagType])) {
                            continue;
                        }
                        $tagIds = array_merge($tagIds, $tags[$keyTagType]);
                    }
                }
                continue;
            }

            if (!is_array($keyFields)) {
                $keyFields = [$keyFields];
            }

            foreach ($keyFields as $keyField) {
                if (isset($data[$keyField])) {
                    $tableField = $keyField;
                    if (strpos($tableField, '.') !== false) {
                        list($table, $field) = explode('.', $tableField, 2);
                        if ($gw->getTable() == $table) {
                            $gw->whereBy($field, $data[$keyField]);
                        }
                    } else {
                        $gw->whereBy($keyField, $data[$keyField]);
                    }
                }
            }
        }

        $tagIds = array_unique($tagIds);
        if (!empty($tagIds)) {
            $gw->whereTagIds($tagIds);
        }

        return $gw->id();
    }

    protected function combine($line)
    {
        $data = [];
        foreach ($this->schema as $k => $v) {
            if (isset($line[$k])) {
                $data[$v] = $line[$k];
            }
        }
        return $data;
    }

    //Завершает задачу импорта
    private function finalize()
    {
        #$this->processMissingStatus();
        return;
        //Удаляем все промежуточные данные
        if (!empty($this->importId)) {
            ImportPreviewGateway::instance()->whereBy('import_id', $this->importId)->delete();
            ImportErrorGateway::instance()->whereBy('import_id', $this->importId)->delete();
        }
    }

    public function processMissingStatus()
    {
        if (!empty($this->missingStatus)) {
            $brandIds = ProductGateway::instance()
                ->innerJoin('cody_import_product_result', array(
                    'product_id' => array('cody_product' => 'product_id'),
                    'import_id' => $this->importId,
                ))
                ->column('DISTINCT brand_id');

            if ($this->missingStatus == 'out') {
                ProductVariantGateway::instance()
                    ->leftJoin('cody_product', 'product_id', 'cody_product_variant', 'product_id')
                    ->whereBy('cody_product.brand_id', $brandIds)
                    ->leftJoin('cody_import_product_result', array(
                        'product_id' => array('cody_product_variant' => 'product_id'),
                        'product_variant_id' => array('cody_product_variant' => 'product_variant_id'),
                        'import_id' => $this->importId,
                    ))
                    ->whereBy('account_id', $this->accountId)
                    ->whereNull('cody_import_product_result.import_product_result')
                    ->update(array('product_variant_amount' => 0));
            } else if ($this->missingStatus == 'hidden') {
                ProductGateway::instance()
                    ->whereBy('brand_id', $brandIds)
                    ->leftJoin('cody_import_product_result', array(
                        'product_id' => array('cody_product' => 'product_id'),
                        'import_id' => $this->importId,
                    ))
                    ->whereBy('account_id', $this->accountId)
                    ->whereNull('cody_import_product_result.import_product_result')
                    ->update(array('product_enabled' => 'N'));
            } else if ($this->missingStatus == 'deleted') {
                ProductVariantGateway::instance()
                    ->leftJoin('cody_product', 'product_id', 'cody_product_variant', 'product_id')
                    ->whereBy('cody_product.brand_id', $brandIds)
                    ->leftJoin('cody_import_product_result', array(
                        'product_id' => array('cody_product_variant' => 'product_id'),
                        'product_variant_id' => array('cody_product_variant' => 'product_variant_id'),
                        'import_id' => $this->importId,
                    ))
                    ->whereBy('account_id', $this->accountId)
                    ->whereNull('cody_import_product_result.import_product_result')
                    ->delete();

                ProductGateway::instance()
                    ->whereBy('brand_id', $brandIds)
                    ->leftJoin('cody_import_product_result', array(
                        'product_id' => array('cody_product' => 'product_id'),
                        'import_id' => $this->importId,
                    ))
                    ->whereBy('account_id', $this->accountId)
                    ->whereNull('cody_import_product_result.import_product_result_id')
                    ->delete();
            }
        }
    }

    //Сохраняет статус импорта
    public function insertImportStats($productId = 0, $variantId = 0, $status = '', $message = '')
    {
        $result['import_id'] = $this->importId;
        $result['product_id'] = $productId;
        $result['product_variant_id'] = $variantId;
        $result['import_product_result'] = $status;
        $result['import_product_result_message'] = $message;

        ImportProductResultGateway::instance()->insert($result);
    }

}
