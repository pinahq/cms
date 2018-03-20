<?php

namespace Pina\Modules\Cart;

use Pina\Modules\CMS\ResourceTypeGateway;
use Pina\Modules\CMS\ContentTypeGateway;
use Pina\InstallationInterface;
use Pina\ModuleRegistry;
use Pina\Modules\CMS\ConfigGateway;

class Installation implements InstallationInterface
{

    public static function install()
    {
        $moduleId = ModuleRegistry::add(new Module());
        
        if (empty($moduleId)) {
            throw new \Exception('can not install module '.__NAMESPACE__);
            return;
        }
        
        self::createResourceType($moduleId);
        self::createContentType($moduleId);
        self::createConfig();
        
        self::createOrderStatuses();
        self::createPaymentMethod();
    }
    
    public static function createResourceType($moduleId)
    {
        $data = array(
            'module_id' => $moduleId,
            'type' => 'products',
            'title' => 'Товар',
        );
        if (!ResourceTypeGateway::instance()->whereFields($data)->exists()) {
            ResourceTypeGateway::instance()->insert($data);
        }
        
        $data = array(
            'module_id' => $moduleId,
            'type' => 'categories',
            'title' => 'Категория',
            'tree' => 'Y',
        );
        if (!ResourceTypeGateway::instance()->whereFields($data)->exists()) {
            ResourceTypeGateway::instance()->insert($data);
        }
        
        $data = array(
            'module_id' => $moduleId,
            'type' => 'collections',
            'title' => 'Бренд',
        );
        if (!ResourceTypeGateway::instance()->whereFields($data)->exists()) {
            ResourceTypeGateway::instance()->insert($data);
        }
        
        $data = array(
            'module_id' => $moduleId,
            'type' => 'collections',
            'title' => 'Набор',
        );
        if (!ResourceTypeGateway::instance()->whereFields($data)->exists()) {
            ResourceTypeGateway::instance()->insert($data);
        }
    }
    
    public static function createContentType($moduleId)
    {
        $data = array(
            'module_id' => $moduleId,
            'type' => 'catalog-matrix-content',
            'title' => 'Баннер и товары',
        );
        if (!ContentTypeGateway::instance()->whereFields($data)->exists()) {
            ContentTypeGateway::instance()->put($data);
        }
        
        $data = array(
            'module_id' => $moduleId,
            'type' => 'sale-content',
            'title' => 'Распродажа',
        );
        if (!ContentTypeGateway::instance()->whereFields($data)->exists()) {
            ContentTypeGateway::instance()->put($data);
        }
    }

    public static function createConfig()
    {
        $priceFormats = [
            '.| ' => '9 999.99', 
            '.|,' => '9,999.99', 
            ',| ' => '9 999,99', 
            ',|.' => '9.999,99'
        ];

        ConfigGateway::instance()->context('namespace', __NAMESPACE__)
            ->context('group', 'Настройки валюты')
            ->context('resource', 'cp/:cp/view-price')->insertIgnore([
                [
                    'key' => 'format_price',
                    'type' => 'select',
                    'variants' => json_encode($priceFormats),
                    'title' => 'Формат отображения валюты',
                    'value' => '',
                    'order' => "1"
                ],
                [
                    'key' => 'prefix_currency_symbol',
                    'type' => 'text',
                    'title' => 'Символ валюты(префикс)',
                    'value' => '',
                    'order' => "2"
                ],
                [
                    'key' => 'suffix_currency_symbol',
                    'type' => 'text',
                    'title' => 'Символ валюты(суффикс)',
                    'value' => '',
                    'order' => "3"
                ],
                [
                    'key' => 'hide_zeros',
                    'type' => 'checkbox',
                    'title' => 'Скрывать нули после запятой',
                    'value' => '',
                    'order' => "4"
                ]
            ]);

            $delimiters = [';' => ';', ',' => ',', "\t" => 'Tab'];
            $charsets = ['utf8' => 'utf8', 'cp1251' => 'cp1251'];

            ConfigGateway::instance()->context('namespace', __NAMESPACE__)
                ->context('group', 'Настройки экспорта в СSV')->insertIgnore([
                [
                    'key' => 'csv_delimiter',
                    'type' => 'select',
                    'variants' => json_encode($delimiters),
                    'title' => 'Разделитель для CSV формата',
                    'value' => ',',
                    'order' => "5"
                ],
                [
                    'key' => 'csv_charset',
                    'type' => 'select',
                    'variants' => json_encode($charsets),
                    'title' => 'Кодировка для CSV фaйла',
                    'value' => 'utf8',
                    'order' => "6"
                ]
            ]);
            

        ConfigGateway::instance()->context('namespace', __NAMESPACE__)
            ->context('group', 'Stock')
            ->insertIgnore([
                [
                    'key' => 'display_out_of_stock',
                    'type' => 'checkbox',
                    'title' => 'Display out of stock products',
                    'value' => 'N',
                    'order' => "7"
                ]
            ]);
    }
    
    public static function createOrderStatuses()
    {
        $data = [
            ["placed", 'placed', "Заказ размещен", 'fedeae'],
            ["approval", 'confirmed', "Наличие подтверждено", 'f2eda9'],
            ["approval", 'substitute', "Предложить замену", 'f2eda9'],
            ["approval", 'approved', "Согласовано с клиентом", 'f2eda9'],
            ["approval", 'prepayed', "Предоплата поступила", 'f2eda9'],
            ["assembling", 'for-assembling', "Передано в комплектацию", 'd6e9a7'],
            ["assembling", 'assembling', "Комплектуется", 'd6e9a7'],
            ["assembling", 'assembled', "Укомплектован", 'd6e9a7'],
            ["delivering", 'for-delivering', 'Передан в доставку', 'c9ede2'],
            ["delivering", 'delivering', 'Доставляется', 'c9ede2'],
            ['delivering', 'delivering-redirected', 'Доставка перенесена', 'c9ede2'],
            ['complete', 'complete', 'Выполнен', 'cfe1e7'],
            ['cancelled', 'failed-call', 'Недозвон', 'f9d6d6'],
            ['cancelled', 'out-of-stock', 'Нет в наличии', 'f9d6d6'],
            ['cancelled', 'already-buyed', 'Купил в другом месте', 'f9d6d6'],
            ['cancelled', 'canceled-delivery', 'Не устроила доставка', 'f9d6d6'],
            ['cancelled', 'canceled-price', 'Не устроила цена', 'f9d6d6'],
            ['cancelled', 'canceled', 'Отменен', 'f9d6d6'],
        ];

        foreach ($data as $item) {
            list($group, $status, $title, $color) = $item;

            if (!OrderStatusGateway::instance()
                            ->whereBy('group', $group)
                            ->whereBy('status', $status)
                            ->exists()) {
                OrderStatusGateway::instance()->insert([
                    'group' => $group,
                    'status' => $status,
                    'title' => $title,
                    'color' => $color,
                ]);
            }
        }

        $data = [
            ['placed', 'Y', "Добавлен", 'fedeae'],
            ['assembled', 'Y', "Укомплектован", 'd6e9a7'],
            ['cancelled', 'N', "Отказ клиента", 'f9d6d6'],
            ['out-of-stock', 'N', 'Нет в наличии', 'f9d6d6'],
        ];
        
        foreach ($data as $item) {
            list($status, $decreased, $title, $color) = $item;

            if (!OrderOfferStatusGateway::instance()
                            ->whereBy('decreased', $decreased)
                            ->whereBy('status', $status)
                            ->exists()) {
                
                OrderOfferStatusGateway::instance()->insert([
                    'decreased' => $decreased,
                    'status' => $status,
                    'title' => $title,
                    'color' => $color,
                ]);
            }
        }

    }

    public static function createPaymentMethod()
    {
        $exists = PaymentMethodGateway::instance()
            ->whereBy('resource', '')
            ->exists();
        if (!$exists) {
            PaymentMethodGateway::instance()->put([
                'title' => "Оплата наличными",
                'order' => "0",
                'enabled' => "Y"
            ]);
        }
    }

    public static function remove()
    {
        #echo 'remove';
    }

}
