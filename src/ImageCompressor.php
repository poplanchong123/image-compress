<?php

namespace Poplanchong123\ImageCompress;

use Poplanchong123\ImageCompress\Exception\ImageCompressException;
use Poplanchong123\ImageCompress\Input\ImageInputInterface;
use Poplanchong123\ImageCompress\Output\ImageOutputInterface;
use Poplanchong123\ImageCompress\Factory\InputFactory;
use Poplanchong123\ImageCompress\Factory\OutputFactory;

class ImageCompressor
{
    private $config = [
        'max_width' => null,
        'max_height' => null,
        'quality' => 85,
        'preserve_aspect_ratio' => true,
        'strip_metadata' => true
    ];

    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 快速压缩方法，自动识别输入类型
     * @param mixed $input 输入源（文件路径、$_FILES数组项或Base64字符串）
     * @param string $outputType 输出类型（'file' 或 'stream'）
     * @param string|null $outputPath 输出文件路径（当outputType为'file'时必需）
     * @return mixed 压缩结果
     * @throws ImageCompressException
     */
    public function quickCompress($input, $outputType, $outputPath = null)
    {
        $inputInstance = InputFactory::create($input);
        $outputInstance = OutputFactory::create($outputType, $outputPath);
        
        return $this->compress($inputInstance, $outputInstance);
    }

    /**
     * 批量快速压缩
     * @param array $inputs 输入源数组
     * @param string $outputType 输出类型
     * @param array $outputPaths 输出路径数组（当outputType为'file'时必需）
     * @return array 压缩结果数组
     * @throws ImageCompressException
     */
    public function quickBatchCompress($inputs, $outputType, $outputPaths = array())
    {
        if ($outputType === OutputFactory::TYPE_FILE && count($inputs) !== count($outputPaths)) {
            throw new ImageCompressException("Number of inputs must match number of output paths");
        }

        $results = [];
        foreach ($inputs as $index => $input) {
            try {
                $outputPath = $outputType === OutputFactory::TYPE_FILE ? $outputPaths[$index] : null;
                $results[$index] = [
                    'success' => true,
                    'result' => $this->quickCompress($input, $outputType, $outputPath)
                ];
            } catch (\Throwable $e) {
                $results[$index] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * 使用自定义输入输出实例压缩
     * @param ImageInputInterface $input 输入实例
     * @param ImageOutputInterface $output 输出实例
     * @return mixed 压缩结果
     * @throws ImageCompressException
     */
    public function compress(ImageInputInterface $input, ImageOutputInterface $output)
    {
        // 获取原始图片资源
        $originalResource = $input->getImageResource();
        $mimeType = $input->getMimeType();

        try {
            // 获取原始尺寸
            $originalWidth = imagesx($originalResource);
            $originalHeight = imagesy($originalResource);

            // 计算新尺寸
            list($newWidth, $newHeight) = $this->calculateDimensions($originalWidth, $originalHeight);

            // 如果尺寸没有变化且质量是100，直接返回原图
            if ($newWidth === $originalWidth && $newHeight === $originalHeight && $this->config['quality'] === 100) {
                return $output->save($originalResource, $mimeType, ['quality' => $this->config['quality']]);
            }

            // 创建新图片
            $newResource = imagecreatetruecolor($newWidth, $newHeight);

            // 处理透明度
            if ($mimeType === 'image/png') {
                imagealphablending($newResource, false);
                imagesavealpha($newResource, true);
            }

            // 重采样
            imagecopyresampled(
                $newResource,
                $originalResource,
                0, 0, 0, 0,
                $newWidth,
                $newHeight,
                $originalWidth,
                $originalHeight
            );

            // 保存处理后的图片
            $result = $output->save($newResource, $mimeType, [
                'quality' => $this->config['quality']
            ]);

            // 清理资源
            imagedestroy($newResource);
            imagedestroy($originalResource);

            return $result;
        } catch (\Throwable $e) {
            // 确保清理资源
            if (isset($newResource)) {
                imagedestroy($newResource);
            }
            if (isset($originalResource)) {
                imagedestroy($originalResource);
            }
            throw new ImageCompressException("Compression failed: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * 计算新的图片尺寸
     * @param int $originalWidth 原始宽度
     * @param int $originalHeight 原始高度
     * @return array [新宽度, 新高度]
     */
    private function calculateDimensions(int $originalWidth, int $originalHeight): array
    {
        $newWidth = $originalWidth;
        $newHeight = $originalHeight;

        // 如果设置了最大宽度或高度，进行缩放
        if ($this->config['max_width'] || $this->config['max_height']) {
            if ($this->config['preserve_aspect_ratio']) {
                // 保持宽高比
                $ratio = $originalWidth / $originalHeight;

                if ($this->config['max_width'] && $originalWidth > $this->config['max_width']) {
                    $newWidth = $this->config['max_width'];
                    $newHeight = (int)round($newWidth / $ratio);
                }

                if ($this->config['max_height'] && $newHeight > $this->config['max_height']) {
                    $newHeight = $this->config['max_height'];
                    $newWidth = (int)round($newHeight * $ratio);
                }
            } else {
                // 不保持宽高比，直接使用最大值
                if ($this->config['max_width']) {
                    $newWidth = min($originalWidth, $this->config['max_width']);
                }
                if ($this->config['max_height']) {
                    $newHeight = min($originalHeight, $this->config['max_height']);
                }
            }
        }

        return [$newWidth, $newHeight];
    }
}