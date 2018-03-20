<?php

namespace Pina\Modules\Images;

use Pina\Log;

class ImageResizer
{

    private $width;
    private $height;
    private $crop;
    private $trim;
    private $mime;

    public function __construct($width = 0, $height = 0, $crop = false, $trim = false)
    {
        $this->width = $width;
        $this->height = $height;
        $this->crop = $crop;
        $this->trim = $trim;
    }

    public function resize($source, $target)
    {
        if (!$this->width && !$this->height) {
            return array(0, 0);
        }
        $sourceImageData = $this->getSourceData($source);
        if ($sourceImageData === false) {
            return array(0, 0);
        }
        $this->mime = $sourceImageData['mime'];
        $imageFormat = $this->getImageFormat($sourceImageData['mime']);
        if ($imageFormat === false) {
            return array(0, 0);
        }
        $imageCreateFunction = 'imagecreatefrom' . $imageFormat;
        if (!function_exists($imageCreateFunction)) {
            Log::error("image.resize", "function ".$imageCreateFunction." does not exists");
            return array(0, 0);
        }
        list($sourceWidth, $sourceHeight) = $sourceImageData;
        if ($sourceWidth > 6000 || $sourceHeight > 6000) {
            return array(0, 0);
        }

        $imageSource = @$imageCreateFunction($source);
        if ($imageSource === false) {
            return array(0, 0);
        }

        list($sourceWidth, $sourceHeight, $sourceLeft, $sourceTop) = $sss = $this->trim($imageSource, $sourceWidth, $sourceHeight);
        list($targetWidth, $targetHeight, $targetLeft, $targetTop) = $ttt = $this->calc($sourceWidth, $sourceHeight);

        $imageTarget = $this->prepareImageTarget();
        if (!imagecopyresampled($imageTarget, $imageSource, $targetLeft, $targetTop, $sourceLeft, $sourceTop, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight)) {
            @imagedestroy($imageSource);
            @imagedestroy($imageTarget);
            return array(0, 0);
        }

        if (empty($target)) {
            header('Content-Type: image/' . $imageFormat);
            header('Content-Disposition: inline; filename="' . basename($source) . '"');
            $imageCreateFunction = 'image' . $imageFormat;
            if (!$imageCreateFunction($imageTarget)) {
                @imagedestroy($imageSource);
                @imagedestroy($imageTarget);
                return array(0, 0);
            }
        } else {
            if (in_array($imageFormat, array('png', 'jpeg', 'gif'))) {
                $imageCreateFunction = 'image' . $imageFormat;
                if (!$imageCreateFunction($imageTarget, $target)) {
                    @imagedestroy($imageSource);
                    @imagedestroy($imageTarget);
                    return array(0, 0);
                }
            }
        }
        @imagedestroy($imageSource);
        @imagedestroy($imageTarget);
        return array($targetWidth, $targetHeight);
    }

    private function getSourceData($source)
    {
        if (!is_file($source)) {
            return false;
        }
        return getimagesize($source);
    }

    private function getImageFormat($mime)
    {
        $format = strtolower(substr($mime, strpos($mime, '/') + 1));
        return in_array($format, array('png', 'jpeg', 'gif')) ? $format : false;
    }

    private function prepareImageTarget()
    {
        $imageTarget = imagecreatetruecolor($this->width, $this->height);
        imagesavealpha($imageTarget, true);
        imagealphablending($imageTarget, false);
        imagefill($imageTarget, 0, 0, 0x7fffffff);
        return $imageTarget;
    }

    private function trim($img, $sourceWidth, $sourceHeight)
    {
        if (empty($this->trim) || $this->trim < 0) {
            return [$sourceWidth, $sourceHeight, 0, 0];
        }
        $colors = $this->getTrimColors($img, $this->trim - 1);

        $top = 0;
        for (; $top < $sourceHeight; ++$top) {
            for ($x = 0; $x < $sourceWidth; ++$x) {
                $currentColor = imagecolorat($img, $x, $top);
                if (!in_array($currentColor, $colors)) {
                    break 2;
                }
            }
        }

        $bottom = $sourceHeight - 1;
        for (; $bottom >= 0; $bottom--) {
            for ($x = 0; $x < $sourceWidth; $x++) {
                $currentColor = imagecolorat($img, $x, $bottom);
                if (!in_array($currentColor, $colors)) {
                    break 2;
                }
            }
        }

        $left = 0;
        for (; $left < $sourceWidth; ++$left) {
            for ($y = 0; $y < $sourceHeight; ++$y) {
                $currentColor = imagecolorat($img, $left, $y);
                if (!in_array($currentColor, $colors)) {
                    break 2;
                }
            }
        }

        $right = $sourceWidth - 1;
        for (; $right >= 0; $right--) {
            for ($y = 0; $y < $sourceHeight; ++$y) {
                $currentColor = imagecolorat($img, $right, $y);
                if (!in_array($currentColor, $colors)) {
                    break 2;
                }
            }
        }
        
        return [$right - $left, $bottom - $top, $left, $top];
    }
    
    protected function getTrimColors($img, $diff = 0)
    {
        $color = imagecolorat($img, 0, 0);
        $colorRgb = imagecolorsforindex($img, $color);

        $colors = [$color];
        for ($i = 1; $i < $diff; $i++) {
            $rgb = $colorRgb;
            $colors [] = imagecolorexact($img, max(0, $colorRgb['red'] - $i), max(0, $colorRgb['green'] - $i), max(0, $colorRgb['blue'] - $i));
            $colors [] = imagecolorexact($img, min(255, $colorRgb['red'] + $i), min(255, $colorRgb['green'] + $i), min(255, $colorRgb['blue'] + $i));
        }
        return array_unique($colors);
    }

    public function calc($sourceWidth, $sourceHeight)
    {
        if (empty($sourceWidth) || empty($sourceHeight)) {
            return array(0, 0, 0, 0);
        }

        $xRatio = $this->width / $sourceWidth;
        $yRatio = $this->height / $sourceHeight;
        if (!$this->height) {
            $yRatio = $xRatio;
            $this->height = floor($yRatio * $sourceHeight);
        } elseif (!$this->width) {
            $xRatio = $yRatio;
            $this->width = floor($xRatio * $sourceWidth);
        }
        $ratio = $this->crop ? max($xRatio, $yRatio) : min($xRatio, $yRatio);
        $ratioByX = $xRatio === $ratio;
        $targetWidth = $ratioByX ? $this->width : floor($sourceWidth * $ratio);
        $targetHeight = $ratioByX ? floor($sourceHeight * $ratio) : $this->height;
        $targetLeft = $ratioByX ? 0 : floor(($this->width - $targetWidth) / 2);
        $targetTop = $ratioByX ? floor(($this->height - $targetHeight) / 2) : 0;
        return array($targetWidth, $targetHeight, $targetLeft, $targetTop);
    }

    public function getSize()
    {
        return array($this->width, $this->height);
    }
    
    public function getMime()
    {
        return $this->mime;
    }

}
