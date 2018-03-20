<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Mail;
use Pina\Arr;


$config = Config::getNamespace(__NAMESPACE__);

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

$s = SubmissionGateway::instance()->find(Request::input('id'));
return [
    'submission' => $s
];