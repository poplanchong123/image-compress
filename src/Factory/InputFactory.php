<?php

namespace Poplanchong123\ImageCompress\Factory;

use Poplanchong123\ImageCompress\Exception\ImageCompressException;
use Poplanchong123\ImageCompress\Input\Base64Input;
use Poplanchong123\ImageCompress\Input\FileInput;
use Poplanchong123\ImageCompress\Input\ImageInputInterface;
use Poplanchong123\ImageCompress\Input\UploadedFileInput;

class InputFactory
{
    /**
     * 创建输入实例
     * @param mixed $input 输入源（文件路径、$_FILES数组项或Base64字符串）
     * @return ImageInputInterface
     * @throws ImageCompressException
     */
    public static function create($input): ImageInputInterface
    {
        if (is_string($input)) {
            // 检查是否是文件路径
            if (file_exists($input)) {
                return new FileInput($input);
            }
            
            // 检查是否是Base64
            if (preg_match('/^data:image\/[a-zA-Z]+;base64,/', $input) || 
                base64_decode($input, true)) {
                return new Base64Input($input);
            }
            
            throw new ImageCompressException('Invalid input: String must be a valid file path or Base64 image data');
        }
        
        // 检查是否是上传文件数组
        if (is_array($input) && isset($input['tmp_name'])) {
            return new UploadedFileInput($input);
        }
        
        throw new ImageCompressException('Invalid input type');
    }
}