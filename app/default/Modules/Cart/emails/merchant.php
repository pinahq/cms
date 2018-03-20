<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Mail;
use Pina\Modules\CMS\Config;
use Pina\Modules\CMS\TagTypeGateway;
use Pina\Modules\CMS\Tag;

$config = Config::getNamespace('Pina\Modules\CMS');

if (!empty($config['submission_emails'])) {
    $submissionEmails = explode(';', $config['submission_emails']);
    $submissionEmail = array_shift($submissionEmails);
    if (filter_var($submissionEmail, FILTER_VALIDATE_EMAIL)) {
        Mail::to($submissionEmail);
        foreach ($submissionEmails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Mail::bcc($email);
            }
        }
    }
} elseif (filter_var($config['company_email'], FILTER_VALIDATE_EMAIL)) {
    Mail::to($config['company_email']);
}

$orderId = Request::input('order_id');

$o = OrderGateway::instance()
    ->select('*')
    ->withStatus()
    ->withCountryAndRegion()
    ->find($orderId);

$oos = OrderOfferGateway::instance()->whereBy('order_id', $orderId)->get();
$tagTypes = TagTypeGateway::instance()->innerJoin(OrderOfferTagTypeGateway::instance()->on('tag_type_id', 'id'))->column('type');
foreach ($oos as $k => $v) {
    $oos[$k]['tags'] = Tag::onlyTypes($oos[$k]['tags'], $tagTypes);
}

return [
    'order' => $o,
    'offers' => $oos,
    'host' => \Pina\App::host(),
];