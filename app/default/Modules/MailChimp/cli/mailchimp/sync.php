<?php

namespace Pina\Modules\MailChimp;

use Pina\Arr;
use Pina\Log;
use Pina\Modules\CMS\Config;
use Pina\Modules\CMS\UserGateway;

try {
    $config = Config::getNamespace(__NAMESPACE__);

    $mc = new Mailchimp($config['api_key']);

    $limit = 100;
    $offset = 0;
    while (($data = $mc->getListMembers($config['list_id'], $limit, $offset)) && !empty($data['members'])) {
        $inserts = [];
        foreach ($data['members'] as $member) {
            if (in_array($member['status'], ['subscribed', 'unsubscribed', 'cleaned', 'pending'])) {
                $inserts[] = [
                    'email' => $member['email_address'],
                    'status' => $member['status'],
                    'result' => serialize($member)
                ];
            }
        }

        if (!empty($inserts)) {
            MailchimpSubscriptionGateway::instance()->put($inserts);
        }

        $offset += $limit;
    }

    $limit = 100;
    $offset = 0;
    while (($users = UserGateway::instance()->whereBy('user_subscribed', 'Y')->limit($offset, $limit)->get()) && !empty($users)) {
        $emails = [];
        foreach ($users as $user) {
            $emails[] = $user['user_email'];
        }

        $subscribedEmails = MailchimpSubscriptionGateway::instance()
            ->whereBy('email', $emails)
            ->column('email');
        
        $notSubscribedEmails = array_diff($emails, $subscribedEmails);
        if (!empty($notSubscribedEmails)) {
            $groupUsers = Arr::groupUnique($users, 'user_email');
            foreach ($notSubscribedEmails as $email) {
                $user = $groupUsers[$email];

                $mergeFields = [
                    'firstname' => $user['user_firstname'],
                    'lastname' => $user['user_lastname'],
                    'email' => $user['user_email'],
                    'phone' => $user['user_phone']
                ];

                $result = $mc->subscribe($config['list_id'], $email, $mergeFields);
                if (empty($result['status']) || !in_array($result['status'], ['subscribed', 'unsubscribed', 'cleaned', 'pending'])) {
                    $sResult = serialize($result);
                    MailchimpSubscriptionGateway::instance()
                        ->put([
                            'email' => $email,
                            'status' => 'error',
                            'result' => $sResult
                        ]);
                    #print_r($result);
                    Log::error('mailchimp.sync', "Ошибка при попытке подписать email $email, error = $sResult");
                }
            }
        }

        $offset += $limit;
    }
} catch (\Exception $e) {
    Log::error('mailchimp.sync', $e->getMessage());
}
