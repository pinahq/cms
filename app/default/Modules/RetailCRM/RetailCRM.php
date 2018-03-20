<?php

namespace Pina\Modules\RetailCRM;

use Pina\Modules\Cart\OrderGateway;
use Pina\Modules\Cart\OrderStatusGateway;
use Pina\Modules\Cart\OrderOfferGateway;
use Pina\Modules\CMS\Config;

use Pina\Log;
use Pina\Arr;

class RetailCRM
{
    static $orderSchema = [
        'firstName' => 'firstname',
        'lastName' => 'lastname',
        'patronymic' => 'middlename',
        'phone' => 'phone',
        'email' => 'email',
        'countryIso' => 'country_key',
        'status' => 'retail_crm_order_status',
        'discount' => 'coupon_discount',
        'customerComment' => 'customer_comment',
        'managerComment' => 'manager_comment',
    ];
    static $itemSchema = [
        'initialPrice' => 'actual_price',
        'purchasePrice' => 'cost_price',
        'productName' => 'resource_title',
        'quantity' => 'amount',
        'status' => 'retail_crm_order_offer_status.status',
    ];
    
    static $site = '';

    private static function convert($line, $schema)
    {
        $r = [];
        foreach ($schema as $target => $source) {
            if (!isset($line[$source])) {
                continue;
            }
            $r[$target] = $line[$source];
        }
        return $r;
    }

    private static function client()
    {
        $config = Config::getNamespace(__NAMESPACE__);

        if (empty($config['url']) || empty($config['key']) || empty($config['site'])) {
            throw new \Exception("На заданы настройки доступа для api");
        }
        
        self::$site = $config['site'];
        
        return new \RetailCrm\ApiClient($config['url'], $config['key']);
    }

    public static function sync($orderId)
    {

        $o = OrderGateway::instance()->select('*')
            ->innerJoin(
                OrderStatusGateway::instance()->on('id', 'order_status_id')
                ->innerJoin(
                    RetailCRMOrderStatusGateway::instance()->on('order_status', 'status')->select('status')
                )
            )
            ->withCountryAndRegion()
            ->find($orderId);

        if (empty($o)) {
            return false;
        }

        $oos = OrderOfferGateway::instance()
            ->select('*')
            ->whereBy('order_id', $orderId)
            ->leftJoin(
                RetailCRMOrderOfferStatusGateway::instance()->on('order_offer_status_id')->select('status')
            )
            ->get();

        $items = [];
        foreach ($oos as $oo) {

            $ps = explode("\n", $oo['tags']);
            $properties = [];
            foreach ($ps as $p) {
                if (strpos($p, ':') === false) {
                    continue;
                }
                list($name, $value) = explode(':', $p);
                $property = [];
                $property['name'] = trim($name);
                $property['value'] = trim($value);
                if ($property['name'] == 'Артикул') {
                    $property['code'] = 'article';
                    $oo['resource_title'] .= ' (#'.$property['value'].')';
                }
                
                $properties[] = $property;
            }

            $item = self::convert($oo, self::$itemSchema);
            $item['offer'] = ['externalId' => $oo['offer_id']];
            $item['properties'] = $properties;
            $items[] = $item;
        }

        $data = self::convert($o, self::$orderSchema);

        $data['externalId'] = $o['id'];
        $data['items'] = $items;
        
        $data['delivery'] = [
            //'code' => 'russian-post',
            'cost' => $o['shipping_subtotal'],
            'address' => [
                'text' => $o['zip'] . ' ' . $o['country'] . ' ' . $o['region'] . ' ' . $o['city'] . ' ' . $o['street']
            ]
        ];

        if ($o['delivery_date'] && $o['delivery_date'] != '0000-00-00') {
            $data['delivery']['date'] = $o['delivery_date'];
        }

        if ($o['delivery_time_from'] && $o['delivery_time_from'] != '00:00:00') {
            if (preg_match('/(\d{2})\:(\d{2})\:(\d{2})/si', $o['delivery_time_from'], $matches)) {
                $data['delivery']['time']['from'] = $matches[1] . ":" . $matches[2];
            }
        }

        if ($o['delivery_time_to'] && $o['delivery_time_to'] != '00:00:00') {
            if (preg_match('/(\d{2}):(\d{2}):(\d{2})/si', $o['delivery_time_to'], $matches)) {
                $data['delivery']['time']['to'] = $matches[1] . ":" . $matches[2];
            }
        }

        /*
        $data['paymentStatus'] = 'not-payed';
        if ($o['payed'] > 0 && $o['payed'] >= $o['total']) {
            $data['paymentStatus'] = 'payed';
        }
        */

        if (!empty($o['user_id'])) {
            #$data['customer'] = ['externalId' => $o['user_id']];
        }

        print_r($data);

        try {
            $client = self::client();
            if (empty($client)) {
                Log::error('retailcrm.sync', 'Wrong connection');
            }

            if (RetailCRMOrderGateway::instance()->whereBy('order_id', $orderId)->exists()) {
                $response = $client->ordersEdit($data, 'externalId', self::$site);
                print_r($response);
            } else {
                $data['delivery']['code'] = 'russian-post';
                $response = $client->ordersCreate($data, self::$site);
                print_r($response);
            }

            if ($response->isSuccessful()) {
                RetailCRMOrderGateway::instance()->insertIgnore([
                    'retail_crm_order_id' => $response->id,
                    'order_id' => $orderId
                ]);
                OrderGateway::instance()->whereId($orderId)->update(['number' => $response->id . 'A']);
            } else {
                $errors = [];
                if (isset($response->errors) && is_array($response->errors)) {
                    foreach ($response->errors as $field => $text) {
                        $errors[] = $text.'('.$field.')';
                    }
                }
                Log::error('retailcrm.sync', "Order sync failed (#".$orderId."). ".$response->errorMsg.": ".implode(', ', $errors));
            }
        } catch (\RetailCrm\Exception\CurlException $e) {
            Log::error('retailcrm.sync', "Connection error: " . $e->getMessage());
        } catch (\InvalidArgumentException $e) {
            Log::error('retailcrm.sync', "Argument error: " . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('retailcrm.sync', "Config error: " . $e->getMessage());
        }

        return true;
    }

    public static function statuses()
    {
        $statuses = [];

        try {
            $client = self::client();
            $response = $client->statusesList();
            $statuses = $response->statuses;
        } catch (\RetailCrm\Exception\CurlException $e) {
            Log::error('retailcrm.statuses', "Connection error: " . $e->getMessage());
        } catch (\InvalidArgumentException $e) {
            Log::error('retailcrm.statuses', "Argument error: " . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('retailcrm.statuses', "Config error: " . $e->getMessage());
        }

        return $statuses;
    }

    public static function productStatuses()
    {
        $productStatuses = [];

        try {
            $client = self::client();
            $response = $client->productStatusesList();
            $productStatuses = $response->productStatuses;
        } catch (\RetailCrm\Exception\CurlException $e) {
            Log::error('retailcrm.product-statuses', "Connection error: " . $e->getMessage());
        } catch (\InvalidArgumentException $e) {
            Log::error('retailcrm.product-statuses', "Argument error: " . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('retailcrm.statuses', "Config error: " . $e->getMessage());
        }

        return $productStatuses;
    }

    public static function update()
    {
        $config = ConfigGateway::instance()->whereNamespace(__NAMESPACE__)->column('value', 'key');

        $site = $config['site'];
        $sinceId = RetailCRMHistorySeedGateway::instance()->whereId($site)->value('seed');

        try {
            $client = self::client();

            $page = 1;
            do {
                $response = $client->ordersHistory(['sinceId' => $sinceId], $page ++);
                foreach ($response->history as $line) {
                    if ($line['source'] === 'api' || $line['order']['site'] !== $site || empty($line['order']['externalId'])) {
                        continue;
                    }

                    if (!$line['order']['externalId']) {
                        continue;
                    }

                    if (!empty($line['created'])) {
                        continue;
                    }

                    if (!empty($line['deleted'])) {
                        #OrderGateway::instance()->whereId($orderId)->update(['order_status' => 'canceled']);
                        continue;
                    }

                    echo "\n======\n";
                    echo $line['field'] . " = " . print_r($line['oldValue'], 1) . " => " . print_r($line['newValue'], 1) . "\n";

                    $subject = 'order';
                    $field = $line['field'];
                    if (strpos($field, '.') !== false) {
                        list($subject, $field) = explode('.', $field, 2);
                    }
                    echo "------\n" . $subject . "\n" . $field . "\n------\n";
                    #OrderGateway::instance()

                    if ($subject === 'order') {
                        self::updateOrder($field, $line);
                    } elseif ($subject === 'order_product') {
                        self::updateOffer($field, $line);
                    } elseif ($subject === 'delivery_address') {
                        self::updateAddress($field, $line);
                    }

                    print_r($line['order']);
                    @print_r($line['item']);
                    
                    RetailCRMHistorySeedGateway::instance()->put(['site' => $site, 'seed' => $line['id']]);
                }
            } while ($response->pagination['currentPage'] < $response->pagination['totalPageCount']);
        } catch (\RetailCrm\Exception\CurlException $e) {
            Log::error('retailcrm.update', "Connection error: " . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('retailcrm.statuses', "Config error: " . $e->getMessage());
        }
    }

    private static function updateOrder($field, $line)
    {
        $orderId = $line['order']['externalId'];
        $crmOrderId = $line['order']['id'];
        
        if (!isset(self::$orderSchema[$field])) {
            $field = self::convertField($field);
        }

        if (!isset(self::$orderSchema[$field])) {
            echo "SCHEMA not found ".$field."\n";
            return false;
        }

        $target = self::$orderSchema[$field];
        $oldValue = $line['oldValue'];
        $newValue = $line['newValue'];

        if ($target === 'retail_crm_order_status') {
            $newValue = self::getOrderStatusId($newValue);
            $oldValue = self::getOrderStatusId($oldValue);
            $target = 'order_status_id';

            if (empty($oldValue)) {
                Log::error("retailcrm.update-order", 'unknown status ' . print_r($line['oldValue'], 1));
                return false;
            }
            
            if (empty($newValue)) {
                Log::error("retailcrm.update-order", 'unknown status ' . print_r($line['newValue'], 1));
                return false;
            }
        }

        OrderGateway::instance()
            ->innerJoin(
                RetailCRMOrderGateway::instance()->on('order_id', 'id')->onBy('retail_crm_order_id', $crmOrderId)
            )
            ->whereId($orderId)
            ->whereBy($target, $oldValue)
            #->debug();
        ->update([$target => $newValue]);
        #print_r([$target => $newValue]);
        
        return true;
    }
    
    private static function updateOffer($field, $line)
    {
        $orderId = $line['order']['externalId'];
        $crmOrderId = $line['order']['id'];
        $orderOfferId = $line['item']['offer']['externalId'];
        
        if (!isset(self::$orderSchema[$field])) {
            $field = self::convertField($field);
        }

        if (!isset(self::$itemSchema[$field])) {
            echo "SCHEMA not found ".$field."\n";
            return false;
        }
        
        $target = self::$itemSchema[$field];
        $oldValue = $line['oldValue'];
        $newValue = $line['newValue'];
        

        if ($target === 'status') {
            $newValue = self::getOrderOfferStatusId($newValue['code']);
            $oldValue = self::getOrderOfferStatusId($oldValue['code']);
            $target = 'order_offer_status_id';

            if (empty($oldValue)) {
                Log::error("retailcrm.update-offer", 'unknown item status ' . $line['oldValue']['code']);
                print_r($line['oldValue']['code']);
                return false;
            }
            
            if (empty($newValue)) {
                Log::error("retailcrm.update-offer", 'unknown item status ' . $line['newValue']['code']);
                print_r($line['newValue']['code']);
                return false;
            }

        }
        
        OrderOfferGateway::instance()
            ->innerJoin(
                RetailCRMOrderGateway::instance()->on('order_id')->onBy('retail_crm_order_id', $crmOrderId)
            )
            ->whereBy('order_id', $orderId)
            ->whereBy('offer_id', $orderOfferId)
            ->whereBy($target, $oldValue)
            #->debug();
        ->update([$target => $newValue]);
        #print_r([$target => $newValue]);

    }
    
    private static function updateAddress($field, $line)
    {
        
    }

    private static function getOrderStatusId($status)
    {
        return OrderStatusGateway::instance()
                ->innerJoin(
                    RetailCRMOrderStatusGateway::instance()->on('order_status')
                    ->whereBy('status', $status)
                )
                ->value('id');
    }
    
    private static function getOrderOfferStatusId($status)
    {
        return RetailCRMOrderOfferStatusGateway::instance()
                ->whereBy('status', $status)
                ->value('order_offer_status_id');
    }
    
    private static function convertField($field)
    {
        if (strpos($field, '_') === false) {
            return $field;
        }
        
        $parts = explode('_', $field);
        $r = $parts[0];
        for ($i = 1; $i < count($parts); $i++) {
            $r .= ucfirst($parts[$i]);
        }
        return $r;
    }

}
