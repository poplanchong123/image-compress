<?php

namespace Poplanchong123\ImageCompress\Factory;

use Poplanchong123\ImageCompress\Exception\ImageCompressException;
use Poplanchong123\ImageCompress\Output\FileOutput;
use Poplanchong123\ImageCompress\Output\StreamOutput;
use Poplanchong123\ImageCompress\Output\ImageOutputInterface;

class OutputFactory
{
    public const TYPE_FILE = 'file';
    public const TYPE_STREAM = 'stream';
    
    /**
     * 创建输出实例
     * @param string $type 输出类型（'file' 或 'stream'）
     * @param string|null $path 当type为'file'时的保存路径
     * @return ImageOutputInterface
     * @throws ImageCompressException
     */
    public static function create($type, $path = null)
    {
        switch ($type) {
            case self::TYPE_FILE:
                if (empty($path)) {
                    throw new ImageCompressException('Output path is required for file output type');
                }
                return new FileOutput($path);
                
            case self::TYPE_STREAM:
                return new StreamOutput();
                
            default:
                throw new ImageCompressException('Invalid output type');
        }
    }
}