<?php

namespace Pina\Modules\CMS;

use Pina\Modules\Auth\UserInterface;
use Pina\Arr;

class User implements UserInterface
{

    public function exists($id)
    {
        return UserGateway::instance()->whereBy('enabled', 'Y')->whereBy('id', $id)->exists();
    }

    public function find($id)
    {
        $user = UserGateway::instance()->whereBy('enabled', 'Y')->find($id);
        if (isset($user['password'])) {
            unset($user['password']);
        }
        return $user;
    }

    public function auth($formData)
    {
        $password = isset($formData['password']) ? $formData['password'] : '';
        $email = isset($formData['email']) ? $formData['email'] : '';

        if (empty($password) || empty($email)) {
            return false;
        }
        
        $user = UserGateway::instance()
            ->whereBy('enabled', 'Y')
            ->whereBy('email', $email)
            ->select('id')
            ->select('password')
            ->first();

        if (!isset($user['id'])) {
            return null;
        }

        if (!Hash::check($password, $user["password"])) {
            return null;
        }
        
        return $user['id'];
    }

    public static function download($gw)
    {
        $schema = [];
        $schema[] = ['lastname', __('Lastname')];
        $schema[] = ['firstname', __('Firstname')];
        $schema[] = ['middlename', __('Middlename')];
        $schema[] = ['phone', __('Phone')];
        $schema[] = ['email', __('Email')];
        $schema[] = ['enabled', __('Enabled')];
        $schema[] = ['subscribed', __('Subscribed')];

        $schema[] = ['utm_source', 'UTM Source'];
        $schema[] = ['utm_medium', 'UTM Medium'];
        $schema[] = ['utm_campaign', 'UTM Campaign'];
        $schema[] = ['utm_term', 'UTM Term'];
        $schema[] = ['utm_content', 'UTM Content'];

        header("Content-Type:application/csv;charset=UTF-8");
        header("Content-Disposition:attachment;filename=\"users.csv\"");
        $us = $gw->get();

        $handle = fopen("php://output", "r+");
        fputcsv($handle, Arr::column($schema, 1));
        foreach ($us as $v) {
            $line = [];
            foreach ($schema as $columnSpec) {
                $column = $columnSpec[0];

                if (!isset($v[$column])) {
                    $line[] = '';
                    continue;
                }

                $line[] = $v[$column];
            }
            fputcsv($handle, $line);
        }
        fclose($handle);
        exit;
    }

}
