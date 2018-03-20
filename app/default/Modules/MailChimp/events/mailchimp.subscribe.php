<?php

namespace Pina\Modules\MailChimp;

use Pina\Event;
use Pina\Log;
use Pina\Mail;
use Pina\Arr;
use Pina\Modules\Users\UserGateway;
use Pina\Modules\CMS\Config;

try {
    $email = Event::data();
    $user = UserGateway::instance()
        ->select('firstname')
        ->select('lastname')
        ->select('email')
        ->select('phone')
        ->whereBy('email', $email)
        ->whereBy('subscribed', 'Y')
        ->first();
    if (!$user) {
        throw new \Exception("$email не существует в бд или поле subscribed != 'Y'");
    }

    $subscribed = MailchimpSubscriptionGateway::instance()
        ->whereBy('email', $email)
        ->whereBy('status', 'subscribed')
        ->exists();
    if ($subscribed) {
        throw new \Exception("$email уже подписан");
    }

    $config = Config::getNamespace(__NAMESPACE__);

    $mc = new Mailchimp($config['api_key']);
    $result = $mc->subscribe($config['list_id'], $email, $user);
    if (!empty($result['status']) && in_array($result['status'], ['subscribed','unsubscribed','cleaned','pending'])) {
        MailchimpSubscriptionGateway::instance()
            ->put([
                'email' => $email,
                'status' => $result['status'],
                'result' => serialize($result)
            ]);
    } else {
        $error = serialize($result);
        throw new \Exception("Ошибка при попытке подписать email $email, error = $error");
    }
} catch (\Exception $e) {
    Log::error('mailchimp.subscribe', $e->getMessage());
}
