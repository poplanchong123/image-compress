<?php

namespace Poplanchong123\ImageCompress\Input;

use Poplanchong123\ImageCompress\Exception\ImageCompressException;

class Base64Input implements ImageInputInterface
{
    private $imageData;
    private $mimeType;
    private $originalSize;

    public function __construct(string $base64String)
    {
        if (strpos($base64String, 'data:image/') === 0) {
            // 处理包含MIME类型的Base64字符串
            $parts = explode(';base64,', $base64String);
            if (count($parts) !== 2) {
                throw new ImageCompressException("Invalid Base64 image string format");
            }
            $this->mimeType = str_replace('data:', '', $parts[0]);
            $this->imageData = base64_decode($parts[1], true);
        } else {
            // 处理纯Base64字符串
            $this->imageData = base64_decode($base64String, true);
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $this->mimeType = $finfo->buffer($this->imageData);
        }

        if ($this->imageData === false) {
            throw new ImageCompressException("Invalid Base64 string");
        }

        $this->originalSize = strlen($this->imageData);

        if (!$this->isValidImageType()) {
            throw new ImageCompressException("Invalid image type: {$this->mimeType}");
        }
    }

    public function getImageResource()
    {
        $resource = imagecreatefromstring($this->imageData);
        
        if ($resource === false) {
            throw new ImageCompressException("Failed to create image resource from Base64 string");
        }

        return $resource;
    }

    public function getMimeType()
    {
        return $this->mimeType;
    }

    public function getOriginalSize()
    {
        return $this->originalSize;
    }

    private function isValidImageType()
    {
        return in_array($this->mimeType, [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif'
        ]);
    }
}