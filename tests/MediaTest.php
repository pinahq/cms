<?php

use PHPUnit\Framework\TestCase;
use Pina\App;
use Pina\Config;
use Pina\Modules\Media\Media;
use Pina\Modules\Media\File;

class MediaTest extends TestCase
{

    public function testSaveFile()
    {
        App::init('test', __DIR__ . '/config');

        $config = Config::get('media', 'local');

        $file = new File(__DIR__ . '/media/homepage.jpg', 'homepage.jpg', 'image/jpeg');
        $data = $file->saveToStorage('local');
        $this->assertEquals('image/jpeg', $file->getMimeType());
        $this->assertEquals(1600, $file->getImageWidth());
        $this->assertEquals(613, $file->getImageHeight());
        $this->assertEquals(220390, $file->getSize());
        $this->assertEquals('fdea996666df5fced230535874c6d6eb', $file->getHash());
        $this->assertEquals('local', $file->getStorageKey());

        $this->assertTrue(strpos($file->getStoragePath(), 'homepage') == 6);

        $path = rtrim($config['root'], '/') . '/' . ltrim($file->getStoragePath(), '/');
        $this->assertTrue(file_exists($path));

        $url = Media::getUrl($file->getStorageKey(), $file->getStoragePath());
        $exptectedUrl = rtrim($config['url']) . '/' . ltrim($file->getStoragePath());
        $this->assertEquals($exptectedUrl, $url);

        $this->deleteDir(__DIR__ . '/public/uploads');
    }

    public function testSaveUrl()
    {
        $file = Media::getUrlCache('http://php.net/images/logos/php-logo.svg', 'php-logo.svg');
        $this->assertTrue($file->exists());
        $this->assertEquals('image/svg', $file->getMimeType());
        $file->moveToStorage('local');
        
        $config = Config::get('media', 'local');
        $path = rtrim($config['root'], '/') . '/' . ltrim($file->getStoragePath(), '/');
        $this->assertTrue(file_exists($path));
        
        $this->assertFalse($file->exists());
        $this->deleteDir(__DIR__ . '/public/uploads');
    }

    protected function deleteDir($dirPath)
    {
        if (!is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

}
