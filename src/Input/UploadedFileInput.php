<?php

namespace Poplanchong123\ImageCompress\Input;

use Poplanchong123\ImageCompress\Exception\ImageCompressException;

class UploadedFileInput implements ImageInputInterface
{
    private string $tempPath;
    private string $mimeType;
    private int $originalSize;

    /**
     * @param array $uploadedFile $_FILES 数组中的文件项
     * @throws ImageCompressException
     */
    public function __construct(array $uploadedFile)
    {
        if (!isset($uploadedFile['tmp_name']) || !isset($uploadedFile['error'])) {
            throw new ImageCompressException('Invalid upload file array structure');
        }

        if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
            throw new ImageCompressException($this->getUploadErrorMessage($uploadedFile['error']));
        }

        if (!is_uploaded_file($uploadedFile['tmp_name'])) {
            throw new ImageCompressException('The file was not uploaded via HTTP POST');
        }

        $this->tempPath = $uploadedFile['tmp_name'];
        $this->mimeType = $this->getMimeTypeFromFile();
        $this->originalSize = filesize($this->tempPath);

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
                $resource = imagecreatefromjpeg($this->tempPath);
                break;
            case 'image/png':
                $resource = imagecreatefrompng($this->tempPath);
                break;
            case 'image/gif':
                $resource = imagecreatefromgif($this->tempPath);
                break;
        }

        if ($resource === false) {
            throw new ImageCompressException("Failed to create image resource from uploaded file");
        }

        return $resource;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getOriginalSize(): int
    {
        return $this->originalSize;
    }

    private function isValidImageType(): bool
    {
        return in_array($this->mimeType, [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif'
        ]);
    }

    private function getMimeTypeFromFile(): string
    {
        // 使用 fileinfo 扩展获取真实的 MIME 类型
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($this->tempPath);

        if ($mimeType === false) {
            throw new ImageCompressException("Failed to determine file MIME type");
        }

        return $mimeType;
    }

    private function getUploadErrorMessage(int $errorCode): string
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload';
            default:
                return 'Unknown upload error';
        }
    }
}