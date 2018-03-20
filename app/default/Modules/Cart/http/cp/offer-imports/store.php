<?php

namespace Pina\Modules\Cart;

use Pina\App;
use Pina\Request;
use Pina\Response;
use Pina\Event;

use Pina\Modules\Import\ImportGateway;
use Pina\Modules\Import\FileUploader;

Request::match("cp/:cp/offer-imports");

//Импорт на основе предыдущего импорта (шаблона)
if (Request::input('import_id')) {
    $data = ImportGateway::instance()->find(Request::input('import_id'));
    unset($data['id']);
    unset($data['row']);
    unset($data['created']);
}

if (Request::input('path_reuse') != 'Y') {
    if (FileUploader::validate('path') === false) {
        return Response::badRequest('Wrong file', 'path');
    }

    list($path, $fileName) = FileUploader::move('path');
    
    if (empty($path) || empty($fileName)) {
        return Response::internalError(__('Can not create file'));
    }

    $data['path'] = $path;
    $data['file_name'] = $fileName;
}

$data['status'] = 'read';
$data['format'] = Request::input('format');
$data['header_row'] = intval(Request::input('header_row'));
$data['start_row'] = intval(Request::input('start_row'));

$fields = Request::input('fields');
$data['fields'] = is_array($fields)?join(',', $fields):'';
$data['missing_status'] = Request::input('missing_status');
$data['mode'] = Request::input('update_mode');

$importId = ImportGateway::instance()->insertGetId($data);

if (empty($importId)) {
    return Response::internalError();
}

Event::trigger('catalog.build-import-preview', $importId);

return Response::created(App::link(
    '/cp/:cp/offer-imports/:import_id', array(
        'import_id' => $importId
    )
));