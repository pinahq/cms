<?php

namespace Pina\Modules\CMS;

use Pina\Request;

class Resource
{

    public static function handleUpdate($resourceId)
    {
        if (empty($resourceId)) {
            return false;
        }

        ResourceGateway::instance()->whereId($resourceId)->update(Request::only('resource', 'title', 'parent_id', 'enabled'));

        self::handleUpdateDescription($resourceId);
        self::handleUpdateTags($resourceId);
        self::handleUpdateImages($resourceId);
        self::handleUpdateMeta($resourceId);
    }

    public static function handleCreate()
    {
        $resourceId = ResourceGateway::instance()->insertGetId(Request::only('resource', 'resource_type_id', 'title', 'parent_id', 'enabled'));
        if (empty($resourceId)) {
            return false;
        }

        Request::set('resource_id', $resourceId);

        self::handleUpdateDescription($resourceId);
        self::handleUpdateTags($resourceId);
        self::handleUpdateImages($resourceId);
        self::handleUpdateMeta($resourceId);

        return $resourceId;
    }

    private static function handleUpdateDescription($resourceId)
    {
        ResourceTextGateway::instance()->put(['resource_id' => $resourceId, 'text' => Request::input('text')]);
    }

    private static function handleUpdateTags($resourceId)
    {
        $tags = array_unique(explode(',', Request::input('tags')));
        ResourceTagGateway::instance()->edit($resourceId, $tags);
    }

    private static function handleUpdateImages($resourceId)
    {
        $imageIds = Request::input('image_ids');
        if (!empty($imageIds) && is_array($imageIds)) {
            $preparedImages = array();
            foreach ($imageIds as $order => $imageId) {
                $preparedImages[] = ['image_id' => $imageId, 'order' => $order];
            }

            $gw = ResourceImageGateway::instance()->context('resource_id', $resourceId);
            $gw->delete();
            $gw->insert($preparedImages);

            ResourceGateway::instance()->whereId($resourceId)->update(['image_id' => reset($imageIds)]);
        }
    }

    public static function fillTreeWithPath($path, $resourceTypeId, $delimiter = '/')
    {

        $title = $path;
        $parentId = 0;
        
        $pos = strrpos($path, $delimiter);
        if ($pos !== false) {
            $left = substr($path, 0, $pos);
            $title = substr($path, $pos + strlen($delimiter));
            
            $parentId = self::fillTreeWithPath($left, $resourceTypeId, $delimiter);
        }

        $data = [
            'title' => $title,
            'parent_id' => $parentId,
            'resource_type_id' => $resourceTypeId
        ];

        $id = ResourceGateway::instance()->whereFields($data)->id();
        if (empty($id)) {
            $data['resource'] = static::generateUniqueResource($data['title']);
            $id = ResourceGateway::instance()->insertGetId($data);
        }

        return $id;
    }

    public static function generateUniqueResource($string)
    {
        $key = strtolower(Transliteration::get($string));
        if (strlen($key) > 60) {
            $key = substr($key, 0, 60);
            $i = strrpos($key, "-");
            if (!empty($i)) {
                $key = substr($key, 0, $i);
            }
        }

        $originalKey = $key;
        $index = 1;
        while (ResourceGateway::instance()->whereBy("resource", $key)->exists()) {
            $key = $originalKey . "-" . $index;
            $index ++;
        }
        return $key;
    }

    private static function handleUpdateMeta($resourceId)
    {
        ResourceMetaGateway::instance()->put([
            'resource_id' => $resourceId,
            'title' => Request::input('meta_title'),
            'description' => Request::input('meta_description'),
            'keywords' => Request::input('meta_keywords'),
        ]);
    }

    public static function handleCopy($resourceId)
    {
        $newResourceId = ResourceGateway::instance()->insertGetId(
            ResourceGateway::instance()->whereId($resourceId)->selectAllExcept(['id', 'resource', 'external_id'])->first()
        );

        $gateways = [
            ResourceTextGateway::instance(),
            ResourceTagGateway::instance(),
            ResourceImageGateway::instance(),
            ResourceMetaGateway::instance(),
        ];

        foreach ($gateways as $gateway) {
            $data = $gateway->whereBy('resource_id', $resourceId)->selectAllExcept('resource_id')->get();
            if (empty($data)) {
                continue;
            }
            $gateway->context('resource_id', $newResourceId)->insert($data);
        }
        return $newResourceId;
    }

}
