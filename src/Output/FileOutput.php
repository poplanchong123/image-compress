<?php

namespace Poplanchong123\ImageCompress\Output;

use Poplanchong123\ImageCompress\Exception\ImageCompressException;

class FileOutput implements ImageOutputInterface
{
    private $outputPath;

    public function __construct($outputPath)
    {
        $this->outputPath = $outputPath;
        
        // 确保输出目录存在
        $directory = dirname($outputPath);
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0777, true)) {
                throw new ImageCompressException("Failed to create output directory: {$directory}");
            }
        }
    }

    public function save($resource, $mimeType, $options = array())
    {
        $quality = isset($options['quality']) ? $options['quality'] : 85;
        $success = false;

        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                $success = imagejpeg($resource, $this->outputPath, $quality);
                break;
            case 'image/png':
                // PNG质量范围是0-9
                $pngQuality = (int)round((100 - $quality) * 9 / 100);
                $success = imagepng($resource, $this->outputPath, $pngQuality);
                break;
            case 'image/gif':
                $success = imagegif($resource, $this->outputPath);
                break;
            default:
                throw new ImageCompressException("Unsupported image type: {$mimeType}");
        }

        if (!$success) {
            throw new ImageCompressException("Failed to save image to: {$this->outputPath}");
        }

        return $this->outputPath;
    }
}