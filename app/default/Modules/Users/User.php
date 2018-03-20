<?php

namespace Pina\Modules\Users;

use Pina\Arr;

class User
{
    public static function download($gw)
    {
        $schema = [];
        $schema[] = ['lastname', __('Lastname')];
        $schema[] = ['firstname', __('Firstname')];
        $schema[] = ['middlename', __('Middlename')];
        $schema[] = ['phone', __('Phone')];
        $schema[] = ['email', __('Email')];
        $schema[] = ['status', __('Status')];
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
