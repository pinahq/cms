<?php

namespace Pina\Modules\YandexKassa;

use Pina\InstallationInterface;
use Pina\ModuleRegistry;

use Pina\Modules\Cart\PaymentMethodGateway;
use Pina\Modules\CMS\ConfigGateway;

class Installation implements InstallationInterface
{
    public static function install()
    {
        $moduleId = ModuleRegistry::add(new Module());

        if (empty($moduleId)) {
            throw new \Exception('can not install module ' . __NAMESPACE__);
            return;
        }

        self::createPaymentMethods();
        self::createConfig();
    }

    public static function createPaymentMethods()
    {
        $exists = PaymentMethodGateway::instance()
            ->whereBy('resource', 'yandex-kassa')
            ->exists();
        if (!$exists) {
            PaymentMethodGateway::instance()->insertIgnore([
                'title' => "Оплата картой",
                'resource' => "yandex-kassa",
                'order' => "1",
                'enabled' => "Y"
            ]);
        }
    }

    public static function createConfig()
    {
        ConfigGateway::instance()->context('namespace', __NAMESPACE__)
            ->context('group', 'Настройки')->insertIgnore([
                [
                    'key' => 'shopId',
                    'title' => 'shopId',
                    'value' => '',
                    'order' => "1"
                ],
                [
                    'key' => 'scid',
                    'title' => 'scid',
                    'value' => '',
                    'order' => "2"
                ],
                [
                    'key' => 'shopPassword',
                    'title' => 'shopPassword',
                    'value' => '',
                    'order' => "3"
                ],
                [
                    'key' => 'paymentType',
                    'title' => 'paymentType',
                    'value' => '',
                    'order' => "4"
                ]
            ]);
    }

    public static function remove()
    {
        #echo 'remove';
    }
}
