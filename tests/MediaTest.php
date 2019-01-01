<?php

use PHPUnit\Framework\TestCase;
use Pina\App;
use Pina\Config;
use Pina\Modules\Media\Media;

class MediaTest extends TestCase
{

    public function testSaveFile()
    {
        App::init('test', __DIR__ . '/config');

        $config = Config::get('media', 'local');

        $data = Media::saveFile('local', __DIR__ . '/media/homepage.jpg', 'homepage.jpg', 'image/jpeg');
        $this->assertEquals('image/jpeg', $data['type']);
        $this->assertEquals(1600, $data['width']);
        $this->assertEquals(613, $data['height']);
        $this->assertEquals(220390, $data['size']);
        $this->assertEquals('fdea996666df5fced230535874c6d6eb', $data['hash']);
        $this->assertEquals('local', $data['storage']);
        
        $this->assertTrue(strpos($data['path'], 'homepage') == 6);

        $path = rtrim($config['root'], '/') . '/' . ltrim($data['path'], '/');
        $this->assertTrue(file_exists($path));
        
        $url = Media::getUrl($data['storage'], $data['path']);
        $exptectedUrl = rtrim($config['url']).'/'.ltrim($data['path']);
        $this->assertEquals($exptectedUrl, $url);

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
