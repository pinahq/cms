<?php

namespace Pina\Modules\RetailCRM;

use Pina\Modules\Cart\OrderStatusGateway;
use Pina\Modules\Cart\OrderOfferStatusGateway;
use Pina\Modules\CMS\ConfigGateway;

use Pina\InstallationInterface;
use Pina\ModuleRegistry;

class Installation implements InstallationInterface
{

    public static function install()
    {
        $moduleId = ModuleRegistry::add(new Module());

        if (empty($moduleId)) {
            throw new \Exception('can not install module ' . __NAMESPACE__);
            return;
        }


        self::createRetailCRMOrderStatuses();
        self::createRetailCRMOrderOfferStatuses();


        self::createConfig();
    }

    public static function createRetailCRMOrderStatuses()
    {
        $statuses = RetailCRM::statuses();
        if (empty($statuses)) {
            return;
        }

        foreach ($statuses as $retailCRMStatus) {

            if (RetailCRMOrderStatusGateway::instance()->whereBy('status', $retailCRMStatus['code'])->exists()) {
                continue;
            }

            $line = [
                'status' => $retailCRMStatus['code'],
                'title' => $retailCRMStatus['name'],
            ];

            $status = OrderStatusGateway::instance()->whereBy('title', $retailCRMStatus['name'])->value('status');
            if (empty($status) && $retailCRMStatus['code'] === 'new') {
                $status = OrderStatusGateway::instance()->whereBy('status', 'placed')->value('status');
            }
            if (!empty($status)) {
                $line['status'] = $status;
            }

            RetailCRMOrderStatusGateway::instance()->insert($line);
        }
    }

    public static function createRetailCRMOrderOfferStatuses()
    {
        $statuses = RetailCRM::productStatuses();
        if (empty($statuses)) {
            return;
        }

        foreach ($statuses as $retailCRMStatus) {
            
            $decreased = $retailCRMStatus['cancelStatus'] == '1' ? 'N' : 'Y';

            $line = [
                'status' => $retailCRMStatus['code'],
                'status_title' => $retailCRMStatus['name'],
                'retail_crm_decreased' => $decreased,
            ];

            $statusId = OrderOfferStatusGateway::instance()
                    ->whereBy('title', $retailCRMStatus['name'])
                    ->whereBy('decreased', $decreased)
                    ->value('id');
            if (!empty($statusId)) {
                $line['order_offer_status_id'] = $statusId;
            }

            $gw = RetailCRMOrderOfferStatusGateway::instance()->whereBy('status', $retailCRMStatus['code']);
            if ($gw->exists()) {
                $gw->update($line);
            } else {
                RetailCRMOrderOfferStatusGateway::instance()->insert($line);
            }
        }
    }

    public static function createConfig()
    {
        ConfigGateway::instance()->context('namespace', __NAMESPACE__)
            ->context('group', 'Настройки')->insertIgnore([
                [
                    'key' => 'url',
                    'title' => 'url',
                    'value' => '',
                    'order' => "1"
                ],
                [
                    'key' => 'key',
                    'title' => 'key',
                    'value' => '',
                    'order' => "2"
                ],
                [
                    'key' => 'site',
                    'title' => 'site',
                    'value' => '',
                    'order' => "3"
                ]
            ]);
    }

    public static function remove()
    {
        #echo 'remove';
    }

}
