<?php

namespace Pina\Modules\Cart;

use Pina\InstallationInterface;
use Pina\Modules\CMS\ResourceTypeGateway;
use Pina\Modules\CMS\ContentTypeGateway;
use Pina\Modules\CMS\ConfigGateway;
use Pina\Modules\CMS\ModuleRegistry;

class Installation implements InstallationInterface
{

    public static function install()
    {
        self::createResourceType();
        self::createContentType();
        self::createConfig();

        self::createOrderStatuses();
        self::createPaymentMethod();
        self::loadDirectories();
    }

    public static function createResourceType()
    {
        $data = array(
            'type' => 'products',
            'title' => 'Товар',
        );
        if (!ResourceTypeGateway::instance()->whereFields($data)->exists()) {
            ResourceTypeGateway::instance()->insert($data);
        }

        $data = array(
            'type' => 'categories',
            'title' => 'Категория',
            'tree' => 'Y',
        );
        if (!ResourceTypeGateway::instance()->whereFields($data)->exists()) {
            ResourceTypeGateway::instance()->insert($data);
        }

        $data = array(
            'type' => 'collections',
            'title' => 'Бренд',
        );
        if (!ResourceTypeGateway::instance()->whereFields($data)->exists()) {
            ResourceTypeGateway::instance()->insert($data);
        }

        $data = array(
            'type' => 'collections',
            'title' => 'Набор',
        );
        if (!ResourceTypeGateway::instance()->whereFields($data)->exists()) {
            ResourceTypeGateway::instance()->insert($data);
        }
    }

    public static function createContentType()
    {
        $data = array(
            'type' => 'catalog-matrix-content',
            'title' => 'Баннер и товары',
        );
        if (!ContentTypeGateway::instance()->whereFields($data)->exists()) {
            ContentTypeGateway::instance()->put($data);
        }

        $data = array(
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

    public static function getStatusGroupColor($group)
    {
        $data = [
            'placed' => 'fedeae',
            'approval' => 'f2eda9',
            'assembling' => 'd6e9a7',
            'delivering' => 'c9ede2',
            'complete' => 'cfe1e7',
            'cancelled' => 'f9d6d6',
        ];

        if (isset($data[$group])) {
            return $data[$group];
        }

        return false;
    }

    public static function createOrderStatuses()
    {
        $data = [
                ["placed", 'placed', "Заказ размещен"],
                ["approval", 'confirmed', "Наличие подтверждено"],
                ["approval", 'substitute', "Предложить замену"],
                ["approval", 'approved', "Согласовано с клиентом"],
                ["approval", 'prepayed', "Предоплата поступила"],
                ["assembling", 'for-assembling', "Передано в комплектацию"],
                ["assembling", 'assembling', "Комплектуется"],
                ["assembling", 'assembled', "Укомплектован"],
                ["delivering", 'for-delivering', 'Передан в доставку'],
                ["delivering", 'delivering', 'Доставляется'],
                ['delivering', 'delivering-redirected', 'Доставка перенесена'],
                ['complete', 'complete', 'Выполнен'],
                ['cancelled', 'failed-call', 'Недозвон'],
                ['cancelled', 'out-of-stock', 'Нет в наличии'],
                ['cancelled', 'already-buyed', 'Купил в другом месте'],
                ['cancelled', 'canceled-delivery', 'Не устроила доставка'],
                ['cancelled', 'canceled-price', 'Не устроила цена'],
                ['cancelled', 'canceled', 'Отменен'],
        ];

        foreach ($data as $item) {
            list($group, $status, $title) = $item;
            $color = self::getStatusGroupColor($group);

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

    public static function loadDirectories()
    {
        $config = \Pina\Modules\CMS\Config::getNamespace(__NAMESPACE__);

        $encoding = empty($config['csv_charset']) ? 'utf8' : $config['csv_charset'];
        $delimiter = empty($config['csv_delimiter']) ? ';' : ($config['csv_delimiter']);
        $enclosure = '"';
        
        $module = new Module();

        $import = new CountryImport($delimiter, $enclosure, $encoding);
        $import->importFromFile($module->getPath().'/data/ru/countries.csv');
        
        $import = new RegionImport($delimiter, $enclosure, $encoding);
        $import->importFromFile($module->getPath().'/data/ru/regions.csv');
        
        $import = new CityImport($delimiter, $enclosure, $encoding);
        $import->importFromFile($module->getPath().'/data/ru/cities.csv');
    }

    public static function remove()
    {
        
    }

}
