<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Poplanchong123\ImageCompress\ImageCompressor;
use Poplanchong123\ImageCompress\Factory\OutputFactory;
use Poplanchong123\ImageCompress\Exception\ImageCompressException;

// 处理上传的图片
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    try {
        // 创建压缩器实例
        $compressor = new ImageCompressor([
            'max_width' => 1920,
            'max_height' => 1080,
            'quality' => 85
        ]);

        // 生成输出文件名（这里使用时间戳防止文件名冲突）
        $outputPath = __DIR__ . '/output/' . time() . '_compressed.jpg';

        // 压缩图片
        $result = $compressor->quickCompress(
            $_FILES['image'],                   // 输入：上传的文件
            OutputFactory::TYPE_FILE,           // 输出类型：文件
            $outputPath                         // 输出路径
        );

        // 返回成功信息
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Image compressed successfully',
            'output_path' => $result
        ]);

    } catch (ImageCompressException $e) {
        // 返回错误信息
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>图片压缩示例</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .upload-form {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .result {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            display: none;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <h1>图片压缩示例</h1>
    <div class="upload-form">
        <form id="uploadForm" enctype="multipart/form-data">
            <input type="file" name="image" accept="image/*" required>
            <button type="submit">上传并压缩</button>
        </form>
    </div>
    <div id="result" class="result"></div>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const resultDiv = document.getElementById('result');

            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                resultDiv.style.display = 'block';
                
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="success">
                            <p>${data.message}</p>
                            <p>输出路径: ${data.output_path}</p>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="error">
                            <p>错误: ${data.message}</p>
                        </div>
                    `;
                }
            } catch (error) {
                resultDiv.style.display = 'block';
                resultDiv.innerHTML = `
                    <div class="error">
                        <p>发生错误: ${error.message}</p>
                    </div>
                `;
            }
        });
    </script>
</body>
</html>