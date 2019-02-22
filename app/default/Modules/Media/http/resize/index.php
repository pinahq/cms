<?php

namespace Pina\Modules\Media;

use Pina\Response;

$resource = \Pina\Input::getResource();

if (strpos($resource, '/../') !== false) {
    return Response::badRequest()->setContent(new \Pina\EmptyContent);
}
$schema = [
    'width' => 'w',
    'height' => 'h',
    'crop' => 'c',
    'trim' => 't',
];
$pattern = '';
foreach ($schema as $p) {
    $pattern .= '(?:'.$p.'([\d]+))?';
}
if (!preg_match('/^resize\/'.$pattern.'\//si', $resource, $matches)) {
    return Response::badRequest()->setContent(new \Pina\EmptyContent);
}

$base = array_shift($matches);
$params = [];
$keys = array_keys($schema);
foreach ($keys as $index => $key) {
    $params[$key] = isset($matches[$index])?$matches[$index]:false;
}

$source = \Pina\App::path().'/../public/'.substr($resource, strlen($base));

if (!file_exists($source)) {
    return \Pina\Response::notFound();
}
$targetPath = \Pina\App::path().'/../public/'.dirname($resource);
if (!file_exists($targetPath)) {
    mkdir($targetPath, 0777, true);
}
$target = \Pina\App::path().'/../public/'.$resource;
$ir = new ImageResizer($params['width'], $params['height'],  $params['crop'], $params['trim']);
$ir->resize($source, $target);

if (!file_exists($target)) {
    return \Pina\Response::internalError()->emptyContent();
}

header('Content-type: '. $ir->getMime());
readfile($target);

exit;