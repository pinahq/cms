<?php

namespace Pina\Modules\CMS;

use Pina\Modules\Media\MediaGateway;
use Pina\Modules\Media\Media;

class ResourceImport extends Import
{

    protected $resourceCreateAllowed = true;
    protected $resourceUpdateAllowed = true;
    protected $pathDelimiter = '//';
    protected $itemResourceTypeId = 0;
    protected $parentResourceTypeId = 0;
    protected $lineTags = [];
    protected $lineTagIds = [];
    protected $lineResourceId = 0;

    public function __construct($importId, $importSchema)
    {
        parent::__construct($importId, $importSchema);

        $this->pathDelimiter = $this->import['path_delimiter'];
        $this->resourceCreateAllowed = $this->settings['resource_mode'] == '' || $this->settings['resource_mode'] == 'create';
        $this->resourceUpdateAllowed = $this->settings['resource_mode'] == '' || $this->settings['resource_mode'] == 'update';

        $this->itemResourceTypeId = !empty($this->settings['item_resource_type_id']) ? $this->settings['item_resource_type_id'] : ResourceTypeGateway::instance()->whereBy('type', 'products')->id();
        $this->parentResourceTypeId = !empty($this->settings['parent_resource_type_id']) ? $this->settings['parent_resource_type_id'] : ResourceTypeGateway::instance()->whereBy('type', 'categories')->id();
    }

    protected function importLine($line)
    {
        $this->lineResourceId = null;

        $this->getTags($line);
        if ($this->getResource($line)) {
            $this->writeResourceTags();
            $this->putResourceText($line);
            $this->getParent($line);
            $this->getImage($line);
        }
    }

    protected function getTags($line)
    {
        $this->lineTags = [];
        $this->lineTagIds = [];

        $tags = $this->extractTags('tag ', $line);

        foreach ($tags as $tag) {
            list($tagType, $tagTitle) = $tag;
            $tagId = TagGateway::instance()->getIdOrAdd($tagType . ': ' . $tagTitle);
            $this->lineTags[$tagType][] = $tagId;
            $this->lineTagIds[] = $tagId;
        }
    }

    protected function getImage($line)
    {
        $first = true;
        foreach ($this->schema as $key => $item) {
            if ($item === 'image' && !empty($line[$key])) {

                $originalUrl = '';
                $mediaId = Media::findUrl($line[$key]);
                if (!$mediaId) {
                    $filename = $this->lineResourceId . '-' . basename($line[$key]);
                    $file = Media::getUrlCache(Transliteration::get($filename));
                    if (!$file->isMimeType('image/*')) {
                        continue;
                    }
                    $file->moveToStorage();
                    $file->setOriginalUrl($line[$key]);
                    $mediaId = $file->saveMeta();
                }

                $isAssigned = ResourceImageGateway::instance()->whereBy('media_id', $mediaId)->whereBy('resource_id', $this->lineResourceId)->exists();
                if ($mediaId && !$isAssigned) {
                    ResourceImageGateway::instance()->put(array(
                        'resource_id' => $this->lineResourceId,
                        'media_id' => $mediaId,
                    ));
                }

                if ($mediaId && $first) {
                    $first = false;
                    ResourceGateway::instance()->whereId($this->lineResourceId)->update(array('media_id' => $mediaId));
                }
            }
        }

        return true;
    }

    protected function getParent($line)
    {
        foreach ($this->schema as $k => $item) {
            if ($item == 'parent' && !empty($line[$k])) {
                
                $resourceId = Resource::fillTreeWithPath(
                        $line[$k], $this->parentResourceTypeId, $this->pathDelimiter
                );

                if (!empty($resourceId)) {
                    ResourceGateway::instance()->whereId($this->lineResourceId)->update(array('parent_id' => $resourceId));
                }
            }
        }
    }

    protected function getResource($line)
    {
        $keyFields = $this->getKeys('resource');
        if (empty($keyFields)) {
            return false;
        }

        $this->lineResourceId = $this->getId(
            $line, ResourceGateway::instance()->whereBy('resource_type_id', $this->itemResourceTypeId), $keyFields, $this->lineTags
        );

        $data = $this->extractResource($line);
        if (empty($this->lineResourceId) && $this->resourceCreateAllowed) {
            $data['resource_type_id'] = $this->itemResourceTypeId;
            $this->lineResourceId = ResourceGateway::instance()->insertGetId($data);
            $this->logResourceImport('added');
            return true;
        } else if ($this->resourceUpdateAllowed) {
            ResourceGateway::instance()->whereId($this->lineResourceId)->update($data);
            $this->logResourceImport('updated');
            return true;
        }
        $this->logResourceImport('skipped');
        return false;
    }

    protected function extractResource($line)
    {
        $data = $this->extract('resource.', $line);
        if (!empty($data['resource']) && ResourceGateway::instance()->whereBy('resource', $data['resource'])->whereNotId($this->lineResourceId)->exists()) {
            unset($data['resource']);
        }
        return $data;
    }

    protected function logResourceImport($status)
    {
        if (empty($this->importId)) {
            return;
        }
        ImportResourceGateway::instance()->insertIgnore([
            'import_id' => $this->importId,
            'resource_id' => $this->lineResourceId,
            'status' => $status,
        ]);
    }

    protected function writeResourceTags()
    {
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

    protected function putResourceText($line)
    {
        $data = $this->extract('resource_text.', $line);
        $data['resource_id'] = $this->lineResourceId;

        if (!isset($data['text'])) {
            return;
        }

        $gw = ResourceTextGateway::instance()->whereBy('resource_id', $this->lineResourceId);

        if ($gw->exists()) {
            $gw->update($data);
        } else {
            $gw->insert($data);
        }
    }

    protected function begin()
    {
        parent::begin();

        ImportResourceGateway::instance()->whereBy('import_id', $this->importId)->delete();
    }

    protected function finalize()
    {
        parent::finalize();

        $this->processMissingStatus();
        $this->detachOldResourceTags();
    }

    protected function processMissingStatus()
    {
        if (!empty($this->settings['resource_missing_status'])) {
            $gw = ResourceGateway::instance()
                ->whereBy('resource_type_id', $this->itemResourceTypeId)
                ->leftJoin(
                ImportResourceGateway::instance()
                ->on('resource_id', 'id')
                ->onBy('import_id', $this->importId)
                ->whereNull('status')
            );
            if ($this->settings['resource_missing_status'] == 'hidden') {
                $gw->update(array('enabled' => 'N'));
            } else if ($this->settings['resource_missing_status'] == 'deleted') {
                $gw->delete();
            }
        }
    }

    protected function detachOldResourceTags()
    {
        ResourceTagGateway::instance()
            ->innerJoin(
                ImportResourceTagGateway::instance()
                ->alias('used_tag_types')
                ->on('resource_id')
                ->on('tag_type_id')
                ->onBy('import_id', $this->importId)
            )
            ->leftJoin(
                ImportResourceTagGateway::instance()
                ->on('resource_id')
                ->on('tag_id')
                ->onBy('import_id', $this->importId)
                ->whereNull('tag_type_id')
            )
            ->delete();
    }

}
