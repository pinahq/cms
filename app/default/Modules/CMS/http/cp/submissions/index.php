<?php

namespace Pina\Modules\CMS;

use Pina\Request;
use Pina\Paging;
use Pina\App;

Request::match('cp/:cp/submissions');

$gw = SubmissionGateway::instance()->select('*')->orderBy('id', 'desc');

if (Request::input('date') === 'today') {
    $gw->whereLike('created', date('Y-m-d').'%');
}


$paging = new Paging(Request::input('page'), Request::input("paging") ? Request::input("paging") : 50);
$gw->paging($paging);


$ss = $gw->get();

foreach ($ss as $k => $s) {
    $ss[$k]['data'] = empty($s['data'])?[]:json_decode($s['data']);
}
        
return [
    'paging' => $paging->fetch(),
    'submissions' => $ss,
];
