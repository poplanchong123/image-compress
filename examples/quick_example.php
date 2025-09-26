<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Poplanchong123\ImageCompress\ImageCompressor;
use Poplanchong123\ImageCompress\Factory\OutputFactory;

// 创建压缩器实例
$compressor = new ImageCompressor([
    'max_width' => 1920,
    'max_height' => 1080,
    'quality' => 85
]);

try {
    // 示例1：压缩本地文件
    $result1 = $compressor->quickCompress(
        __DIR__ . '/input/test.jpg',                    // 输入：本地文件路径
        OutputFactory::TYPE_FILE,               // 输出类型：文件
        __DIR__ . '/output/compressed1.jpg'     // 输出路径
    );
    echo "File compressed successfully. Output: $result1\n";

    // 示例2：处理上传的文件
    if (isset($_FILES['image'])) {
        $result2 = $compressor->quickCompress(
            $_FILES['image'],                   // 输入：$_FILES数组项
            OutputFactory::TYPE_FILE,           // 输出类型：文件
            __DIR__ . '/output/compressed2.jpg' // 输出路径
        );
        echo "Uploaded file compressed successfully. Output: $result2\n";
    }

    // 示例3：处理Base64图片数据
    $base64Image = "data:image/jpeg;base64," . base64_encode(file_get_contents(__DIR__ . '/input/test.jpg'));
    $result3 = $compressor->quickCompress(
        $base64Image,                          // 输入：Base64字符串
        OutputFactory::TYPE_STREAM             // 输出类型：数据流
    );
    echo "Base64 image compressed successfully. Output size: " . strlen($result3) . " bytes\n";

    // 示例4：批量处理
    $inputs = [
        __DIR__ . '/input/test.jpg',           // 文件路径
        $_FILES['image2'],                     // 上传文件
        $base64Image                           // Base64数据
    ];
    
    $outputPaths = [
        __DIR__ . '/output/batch1.jpg',
        __DIR__ . '/output/batch2.jpg',
        __DIR__ . '/output/batch3.jpg'
    ];
    
    $results = $compressor->quickBatchCompress(
        $inputs,                               // 输入数组
        OutputFactory::TYPE_FILE,              // 统一使用文件输出
        $outputPaths                           // 输出路径数组
    );

    foreach ($results as $index => $result) {
        if ($result['success']) {
            echo "Batch item {$index} compressed successfully\n";
        } else {
            echo "Batch item {$index} compression failed: {$result['error']}\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}