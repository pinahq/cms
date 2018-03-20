<?php

namespace Pina\Modules\CMS;


class ContentManager
{

    public static function addContent($resource, $slot, $text = '', $params = '', $contentType)
    {
        $contentTypeId = ContentTypeGateway::instance()->whereBy('type', $contentType)->value('id');

        $text = is_string($text) ? $text : '';
        $params = is_string($params) ? $params : '';
        
        if (!$contentTypeId ||
            (empty($text) && empty($params))
        ) {
            return false;
        }

        $lastOrder = ContentGateway::instance()
            ->whereBy('slot', $slot)
            ->whereBy('resource_id', $resource)
            ->max('`order`');

        return ContentGateway::instance()
            ->insertGetId([
                'slot' => $slot,
                'resource_id' => $resource,
                'text' => $text,
                'params' => $params,
                'content_type_id' => $contentTypeId,
                'order' => $lastOrder + 1
            ]);
    }

    public static function removeContent($id)
    {
        if (!$id) {
            return false;
        }

        return ContentGateway::instance()
            ->whereBy('id', $id)
            ->delete();
    }

    public static function updateContent($id, $text = '', $params = '')
    {
        if (!$id || (empty($text) && empty($params))) {
            return false;
        }

        $data = [
            'text' => $text,
            'params' => $params
        ];

        ContentGateway::instance()
            ->whereId($id)
            ->update($data);

        return true;
    }

    public static function insertContentAfter($currentId, $prevId)
    {
        if (!$currentId) {
            return false;
        }

        $contentData = ContentGateway::instance()->find($currentId);
        if (!$contentData) {
            return false;
        }

        $contents = ContentGateway::instance()
            ->whereBy('slot', $contentData['slot'])
            ->whereBy('resource_id', $contentData['resource_id'])
            ->whereNotBy('id', $currentId)
            ->orderBy('order','ASC')
            ->orderBy('id', 'ASC')
            ->column('id');

        $contentOrder = self::sortAfter($contents, $currentId, $prevId);

        ContentGateway::instance()->startTransaction();
        foreach ($contentOrder as $order => $contentId) {
            ContentGateway::instance()
                ->whereId($contentId)
                ->update([
                    'order' => $order + 1
                ]);
        }
        ContentGateway::instance()->commit();
    }

    private static function sortAfter($contents, $currentId, $prevId)
    {
        $contentOrder = [];
        $found = false;

        if ($prevId == 0) {
            $contentOrder[] = $currentId;
            $found = true;
        }

        foreach ($contents as $cid) {
            $contentOrder[] = $cid;

            if ($prevId == $cid) {
                $found = true;
                $contentOrder[] = $currentId;
            }
        }

        if (!$found) {
            $contentOrder[] = $currentId;
        }

        return $contentOrder;
    }
}
