<?php

namespace Poplanchong123\ImageCompress\Input;

use Poplanchong123\ImageCompress\Exception\ImageCompressException;

class FileInput implements ImageInputInterface
{
    private $filePath;
    private $mimeType;
    private $originalSize;

    public function __construct(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new ImageCompressException("File not found: {$filePath}");
        }

        $this->filePath = $filePath;
        $this->mimeType = mime_content_type($filePath);
        $this->originalSize = filesize($filePath);

        if (!$this->isValidImageType()) {
            throw new ImageCompressException("Invalid image type: {$this->mimeType}");
        }
    }

    public function getImageResource()
    {
        $resource = false;
        
        switch ($this->mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                $resource = imagecreatefromjpeg($this->filePath);
                break;
            case 'image/png':
                $resource = imagecreatefrompng($this->filePath);
                break;
            case 'image/gif':
                $resource = imagecreatefromgif($this->filePath);
                break;
        }

        if ($resource === false) {
            throw new ImageCompressException("Failed to create image resource from file");
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