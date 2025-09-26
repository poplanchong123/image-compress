<?php

namespace Poplanchong123\ImageCompress\Output;

interface ImageOutputInterface
{
    /**
     * 保存压缩后的图片
     * @param resource $resource 图片资源
     * @param string $mimeType MIME类型
     * @param array $options 输出选项
     * @return mixed 根据实现返回不同的结果（文件路径、二进制数据等）
     */
    public function save($resource, string $mimeType, array $options = []): mixed;
}