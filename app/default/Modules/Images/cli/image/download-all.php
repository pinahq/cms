<?php

namespace Pina\Modules\Images;

while ($images = ImageGateway::instance()
->select('*')
->whereNotBy('url', '')
->whereBy('filename', '')
->limit(100)
->get()) {
    echo 'found ' . count($images) . " items\n";

    foreach ($images as $image) {
        
        echo $image['id'].')'.$image['url']."\n";

        $filename = $image['id'] . '.jpg';
        $source = $image['url'];

        $pathinfo = pathinfo($filename);
        print_r($pathinfo);

        $ext = !empty($pathinfo["extension"]) ? strtolower($pathinfo["extension"]) : '';

        $souce_filename = strtolower(\Pina\Token::translit($pathinfo["filename"]));
        $filename = ImageDomain::newFileName($souce_filename, $ext);

        $filename = ImageDomain::prepareFilename($source, $filename);

        $content = @file_get_contents($source);
        if (empty($content)) {
            continue;
        }

        $filePath = ImageDomain::getFilePath($filename);
        ImageDomain::prepareDir($filename);

        if (!@file_put_contents($filePath, $content)) {
            unset($content);
            Log::error('image', 'ImageDomain can not write to file ' . $filePath);
            return false;
        }
        unset($content);

        $path = $filePath;
        
        $data = [];
        $imgInfo = getimagesize($path);
        if (empty($imgInfo)) {
            $pathInfo = pathinfo($path);
            if ($pathInfo['extension'] !== 'ico' || !file_exists($path)) {
                return false;
            }
            $imgInfo = array(
                0 => 0,
                1 => 0,
                'mime' => 'image/vnd.microsoft.icon'
            );
        }
        $data['hash'] = md5_file($path);
        $data['filename'] = $filename;
        list($data['width'], $data['height']) = $imgInfo;
        $data['type'] = $imgInfo['mime'];
        $data['size'] = filesize(ImageDomain::getFilePath($filename));
        $data['url'] = '';
        
        ImageGateway::instance()->whereId($image['id'])->update($data);
    }
}

