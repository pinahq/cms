<?php

namespace Pina\Modules\PayPal;

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
            ->whereBy('resource', 'paypal')
            ->exists();
        if (!$exists) {
            PaymentMethodGateway::instance()->insertIgnore([
                'title' => "Оплата картой",
                'resource' => "paypal",
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
                'key' => 'mode',
                'title' => 'Mode',
                'type' => 'select',
                'variants' => json_encode(['live' => 'Live', 'sandbox' => 'Sandbox']),
                'value' => 'live',
                'order' => "0"
            ],
                [
                'key' => 'acct1.UserName',
                'title' => 'API Username',
                'type' => 'text',
                'variants' => '',
                'value' => '',
                'order' => "1"
            ],
                [
                'key' => 'acct1.Password',
                'title' => 'API Password',
                'type' => 'text',
                'variants' => '',
                'value' => '',
                'order' => "2"
            ],
                [
                'key' => 'acct1.Signature',
                'title' => 'Signature',
                'type' => 'text',
                'variants' => '',
                'value' => '',
                'order' => "3"
            ],
                [
                'key' => 'description',
                'title' => 'Description of the purchase',
                'type' => 'text',
                'variants' => '',
                'value' => '',
                'order' => "4"
            ],
                [
                'key' => 'currency',
                'title' => 'Currency',
                'type' => 'select',
                'variants' => json_encode([
                    'AUD' => 'Australian Dollar',
                    'BRL' => 'Brazilian Real',
                    'CAD' => 'Canadian Dollar',
                    'CZK' => 'Czech Koruna',
                    'DKK' => 'Danish Krone',
                    'EUR' => 'Euro',
                    'HKD' => 'Hong Kong Dollar',
//'HUF' => 'Hungarian Forint',
                    'ILS' => 'Israeli New Sheqel',
//'JPY' => 'Japanese Yen'
                    'MYR' => 'Malaysian Ringgit',
                    'MXN' => 'Mexican Peso',
                    'NOK' => 'Norwegian Krone',
                    'NZD' => 'New Zealand Dollar',
                    'PHP' => 'Philippine Peso',
                    'PLN' => 'Polish Zloty',
                    'GBP' => 'Pound Sterling',
                    'RUB' => 'Russian Ruble',
                    'SGD' => 'Singapore Dollar',
                    'SEK' => 'Swedish Krona',
                    'CHF' => 'Swiss Franc',
//'TWD' => 'Taiwan New Dollar',
                    'THB' => 'Thai Baht',
                    'USD' => 'U.S. Dollar',
                    ], JSON_UNESCAPED_UNICODE),
                'value' => 'RUB',
                'order' => "4"
            ],
        ]);
    }

    public static function remove()
    {
        #echo 'remove';
    }

}
