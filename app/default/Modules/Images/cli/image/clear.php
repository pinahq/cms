<?php

namespace Pina\Modules\Images;

use Pina\Request;
use Pina\Log;
use Pina\CLI;
use Pina\Modules\CMS\ResourceImageGateway;
use Pina\Modules\CMS\ResourceGateway;
use Pina\Modules\Cart\OrderOfferGateway;
use Pina\Modules\Users\UserGateway;
use Pina\Modules\CMS\ConfigGateway;

$images = ImageGateway::instance()
    ->select('*')
    ->leftJoin(
        ResourceImageGateway::instance()->on('image_id', 'id')->whereNull('resource_id')
    )
    ->leftJoin(
        ResourceGateway::instance()->on('image_id', 'id')->whereNull('resource_id')
    )
    ->leftJoin(
        OrderOfferGateway::instance()->on('image_id', 'id')->whereNull('order_id')
    )
    ->leftJoin(
        UserGateway::instance()->on('image_id', 'id')->whereNull('user_id')
    )
    ->leftJoin(
        ConfigGateway::instance()->on('value', 'id')->whereNull('key')
    )
    ->get();

echo 'found '.count($images)." items\n";

foreach ($images as $image) {
    $contentExists = \Pina\Modules\CMS\ContentGateway::instance()->whereLike('params', '%"image_id":"'.$image['id'].'"%')->exists();
    if ($contentExists) {
        echo "SKIP ". $image['id']."\n";
        continue;
    }

    $hasDouble = ImageGateway::instance()->whereBy('filename', $image['filename'])->whereNotBy('id', $image['id'])->exists();
    if ($hasDouble) {
        echo "HAS DOUBLE ".$image['id']."\n";
        continue;
    }
    
    echo "ID: ".$image['id']."\n";
    $path = ImageDomain::getFilePath($image['filename']);
    echo $path."\n";
    unlink($path);
    if (!file_exists($path)) {
        ImageGateway::instance()->whereBy('image_id', $image['id'])->delete();
    }
        
    $resizes = ImageResizeGateway::instance()->whereBy('image_id', $image['id'])->get();
    foreach ($resizes as $resize) {
        $path = ImageResizeFileManager::getFilePath($resize['filename']);
        echo $path."\n";
        unlink($path);
        if (!file_exists($path)) {
            ImageResizeGateway::instance()->whereBy('id', $resize['id'])->delete();
        }
    }
}