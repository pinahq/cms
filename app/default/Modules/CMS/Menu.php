<?php

namespace Pina\Modules\CMS;

class Menu
{

    private static $items = null;

    public static function get($key, $title)
    {
        $menu = MenuGateway::instance()->find($key);
        if (empty($menu)) {
            MenuGateway::instance()->insertIgnore(['key' => $key, 'title' => $title]);
            return [$title, []];
        }

        if (!isset(self::$items)) {
            self::$items = MenuItemGateway::instance()
                ->select('menu_key')
                ->select('title')
                ->select('link')
                ->whereBy('enabled', 'Y')
                ->leftJoin(
                    ResourceGateway::instance()->on('id', 'resource_id')
                    ->selectAs('enabled', 'resource_enabled')
                    ->leftJoin(
                        ResourceUrlGateway::instance()->on('resource_id', 'id')->selectAs('url', 'resource_url')
                    )
                )
                ->orderBy('order', 'asc')
                ->having("resource_enabled IS NULL or resource_enabled = 'Y'")
                ->get();
        }

        $items = [];
        foreach (self::$items as $item) {
            if ($item['menu_key'] == $key) {
                $items [] = $item;
            }
        }

        return [$menu['title'], $items];
    }

}
