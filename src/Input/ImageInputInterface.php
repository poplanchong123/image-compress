<?php

namespace Poplanchong123\ImageCompress\Input;

interface ImageInputInterface
{
    /**
     * 获取图片资源
     * @return resource|false 返回图片资源或false（如果失败）
     */
    public function getImageResource();
    
    /**
     * 获取图片MIME类型
     * @return string
     */
    public function getMimeType(): string;
    
    /**
     * 获取原始图片大小（字节）
     * @return int
     */
    public function getOriginalSize(): int;
}