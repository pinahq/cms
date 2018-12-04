<?php

namespace Pina\Modules\MailChimp;

use Pina\Event;
use Pina\Log;
use Pina\Mail;
use Pina\Arr;
use Pina\Modules\CMS\UserGateway;
use Pina\Modules\CMS\Config;

try {
    $email = Event::data();
    $user = UserGateway::instance()
        ->whereBy('email', $email)
        ->whereBy('subscribed', 'N')
        ->first();
    if (!$user) {
        throw new \Exception("$email не существует в бд или поле subscribed != 'N'");
    }

    $unsubscribed = MailchimpSubscriptionGateway::instance()
        ->whereBy('email', $email)
        ->whereBy('status', 'unsubscribed')
        ->exists();
    if ($unsubscribed) {
        throw new \Exception("$email уже отписан");
    }

    $config = Config::getNamespace(__NAMESPACE__);

    $mc = new Mailchimp($config['api_key']);
    $result = $mc->unsubscribe($config['list_id'], $email);
    if (!empty($result['status']) && in_array($result['status'], ['subscribed','unsubscribed','cleaned','pending'])) {
        MailchimpSubscriptionGateway::instance()
            ->put([
                'email' => $email,
                'status' => $result['status'],
                'result' => serialize($result)
            ]);
    } else {
        $error = serialize($result);
        throw new \Exception("Ошибка при попытке отписать email $email, error = $error");
    }
} catch (\Exception $e) {
    Log::error('mailchimp.unsubscribe', $e->getMessage());
}
