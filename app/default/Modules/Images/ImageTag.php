<?php

namespace Pina\Modules\Images;

use Pina\App;

class ImageTag
{

    private $width = 0;
    private $height = 0;
    private $params = array();
    private $resizeNow = false;

    public function __construct($params)
    {
        $this->params = $params;
    }
    
    public function resizeNow()
    {
        $this->resizeNow = true;
    }

    public function render()
    {
        $image = $this->findImage();
        if (empty($image)) {
            return '';
        }
        
        if (!empty($image['url'])) {
            $this->addSizesToStyle();
            $src = $image['url'];
        }
        
        if (empty($src) && $this->isResizeRequired($image)) {
            $url = !empty($image['url'])?$image['url']:ImageDomain::getFileUrl($image['filename']);
            $parsed = parse_url($url);
            $resizeMode = '';
            if (!empty($this->params['width'])) {
                $resizeMode .= 'w'.$this->params['width'];
            }
            if (!empty($this->params['height'])) {
                $resizeMode .= 'h'.$this->params['height'];
            }
            if (!empty($this->params['crop'])) {
                $resizeMode .= 'c'.$this->params['crop'];
            }
            if (!empty($this->params['trim'])) {
                $resizeMode .= 't'.$this->params['trim'];
            }
            $src = $parsed['scheme'].'://'.$parsed['host'].'/resize/'.$resizeMode.$parsed['path'];
      
            /*
            $resize = $this->getResize($image);
            if (!empty($resize['filename'])) {
                $src = ImageResizeFileManager::getFileUrl($resize['filename']);
            }
            if (empty($src)) {
                if (empty($resize['id'])) {
                    $imageResizeId = ImageResizeGateway::instance()->insertIgnoreGetId($this->getResizeFields($image['id']));
                } else {
                    $imageResizeId = $resize['id'];
                }
                $src = App::link('images/:id', ['id' => $imageResizeId]);
            }
         */
        } else if (empty($src) && !empty($image['filename'])) {
            $src = ImageDomain::getFileUrl($image['filename']);
        }

        if (empty($src)) {
            return '';
        }

        if (!empty($this->params['return']) && $this->params['return'] === 'src') {
            return $src;
        }
        if ($this->params['show_dimensions'] && !empty($this->width) && !empty($this->height)) {
            $this->addSizesToStyle();
        }
        return $this->getTag($src);
    }

    private function findImage()
    {
        if (!empty($this->params['image']) && (!empty($this->params['image']['filename']) ||  !empty($this->params['image']['url']))) {
            return $this->params['image'];
        }
        
        if (!empty($this->params['image']) && !empty($this->params['image']['id'])) {
            return ImageGateway::instance()->find($this->params['image']['id']);
        }
        
        if (!empty($this->params['id'])) {
            return ImageGateway::instance()->find($this->params['id']);
        }
        if (!empty($this->params['filename'])) {
            return ImageGateway::instance()->whereBy('filename', $this->params['filename'])->first();
        }
        return array();
    }

    private function addSizesToStyle()
    {
        if (!isset($this->params['style'])) {
            $this->params['style'] = '';
        } else if (strpos($this->params['style'], 'width:') !== false || strpos($this->params['style'], 'height:') !== false) {
            return;
        }
        foreach (array('width', 'height') as $p) {
            $v = empty($this->$p) ? (empty($this->params[$p]) ? '' : $this->params[$p]) : $this->$p;
            if ($v) {
                $this->params['style'] .= $p . ':' . $v . 'px;';
            }
        }
    }

    private function getTag($src)
    {
        $img = $this->getTagByParams($src, $this->params);
        if (!empty($this->params['lazy'])) {
            $img .= '<noscript>';
            $params = $this->params;
            unset($params['lazy']);
            $img .= $this->getTagByParams($src, $params);
            $img .= '</noscript>';
        }
        return $img;
    }
    
    private function getTagByParams($src, $params)
    {
        $img = '<img '.(!empty($params['lazy'])?$params['lazy']:'src').'="' . $src . '"';
        foreach (array('alt', 'class', 'style', 'title') as $p) {
            $img .= empty($params[$p]) ? '' : ' ' . $p . '="' . $params[$p] . '"';
        }
        $img .= ' />';
        return $img;
    }

    private function isResizeRequired($image)
    {
        return 
            !empty($this->params['width']) && ($this->params['width'] != $image['width'])
            || !empty($this->params['height']) && ($this->params['height'] != $image['height'])
            || !empty($this->params['trim']) && (!empty($this->params['width']) || !empty($this->params['height']));
    }

    private function getResize($image)
    {
        $this->calcSize($image);
        
        $conditions = $this->getResizeFields($image['id']);
        $resize = ImageResizeGateway::instance()
            ->whereFields($conditions)
            ->first();
        return $resize;
    }
    
    private function calcSize($image)
    {
        $ir = new ImageResizer(
            !empty($this->params['width'])?$this->params['width']:false, 
            !empty($this->params['height'])?$this->params['height']:false, 
            !empty($this->params['crop'])?$this->params['crop']:false,
            !empty($this->params['trim'])?$this->params['trim']:false
        );
        $ir->calc($image['width'], $image['height']);
        list($this->width, $this->height) = $ir->getSize();
    }

    private function getResizeFields($imageId)
    {
        $condition = array('image_id' => $imageId);
        if (!empty($this->width)) {
            $condition['width'] = $this->width;
        }
        if (!empty($this->height)) {
            $condition['height'] = $this->height;
        }
        $condition['crop'] = !empty($this->params['crop'])?$this->params['crop']:0;
        $condition['trim'] = !empty($this->params['trim'])?$this->params['trim']:0;
        return $condition;
    }
}
