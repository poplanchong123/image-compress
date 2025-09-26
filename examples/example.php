<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Poplanchong123\ImageCompress\ImageCompressor;
use Poplanchong123\ImageCompress\Input\FileInput;
use Poplanchong123\ImageCompress\Input\Base64Input;
use Poplanchong123\ImageCompress\Output\FileOutput;
use Poplanchong123\ImageCompress\Output\StreamOutput;

// 创建压缩器实例，设置配置选项
$compressor = new ImageCompressor([
    'max_width' => 1920,        // 最大宽度
    'max_height' => 1080,       // 最大高度
    'quality' => 85,           // 压缩质量
    'preserve_aspect_ratio' => true,  // 保持宽高比
    'strip_metadata' => true    // 去除元数据
]);

try {
    // 示例1：文件输入 -> 文件输出
    $input = new FileInput(__DIR__ . '/input/test.jpg');
    $output = new FileOutput(__DIR__ . '/output/compressed.jpg');
    $result = $compressor->compress($input, $output);
    echo "File compressed successfully. Output: " . $result . "\n";

    // 示例2：Base64输入 -> 流输出
    $base64Image = "data:image/jpeg;base64," . base64_encode(file_get_contents(__DIR__ . '/input/test.jpg'));
    $input = new Base64Input($base64Image);
    $output = new StreamOutput();
    $imageData = $compressor->compress($input, $output);
    echo "Image compressed to stream. Size: " . strlen($imageData) . " bytes\n";

    // 示例3：批量处理
    $inputs = [
        new FileInput(__DIR__ . '/input/image1.jpg'),
        new FileInput(__DIR__ . '/input/image2.png')
    ];
    
    $outputs = [
        new FileOutput(__DIR__ . '/output/compressed1.jpg'),
        new FileOutput(__DIR__ . '/output/compressed2.png')
    ];
    
    $results = $compressor->batchCompress($inputs, $outputs);
    foreach ($results as $index => $result) {
        if ($result['success']) {
            echo "Image {$index} compressed successfully\n";
        } else {
            echo "Image {$index} compression failed: {$result['error']}\n";
        }
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}