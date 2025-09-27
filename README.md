# Image Compress

一个强大的PHP图片压缩库，提供类似TinyPNG的功能。

## 特性

- 支持多种图片格式（JPG/JPEG、PNG、GIF）
- 智能压缩算法，在保证画质的前提下减小文件体积
- 支持等比缩放图片尺寸
- 多种输入方式支持：
  - 上传图片文件
  - 本地图片路径
  - Base64编码的图片数据
- 灵活的输出选项：
  - 保存到指定路径
  - 返回文件流
- 批量处理支持
- 完善的错误处理机制

## 安装

```bash
composer require poplanchong123/image-compress
```

## 要求

- PHP >= 5.6
- GD扩展
- Fileinfo扩展

## 快速开始

```php
use Poplanchong123\ImageCompress\ImageCompressor;
use Poplanchong123\ImageCompress\Input\FileInput;
use Poplanchong123\ImageCompress\Output\FileOutput;

// 创建压缩器实例
$compressor = new ImageCompressor([
    'max_width' => 1920,
    'max_height' => 1080,
    'quality' => 85
]);

// 压缩图片
$input = new FileInput('path/to/input.jpg');
$output = new FileOutput('path/to/output.jpg');
$result = $compressor->compress($input, $output);
```

## 配置选项

- `max_width`: 最大宽度（像素）
- `max_height`: 最大高度（像素）
- `quality`: 压缩质量（1-100）
- `preserve_aspect_ratio`: 是否保持宽高比（默认：true）
- `strip_metadata`: 是否去除元数据（默认：true）

## 输入方式

### 文件输入

```php
use Poplanchong123\ImageCompress\Input\FileInput;

$input = new FileInput('path/to/image.jpg');
```

### Base64输入

```php
use Poplanchong123\ImageCompress\Input\Base64Input;

$input = new Base64Input($base64String);
```

## 输出方式

### 文件输出

```php
use Poplanchong123\ImageCompress\Output\FileOutput;

$output = new FileOutput('path/to/output.jpg');
```

### 流输出

```php
use Poplanchong123\ImageCompress\Output\StreamOutput;

$output = new StreamOutput();
$imageData = $compressor->compress($input, $output);
```

## 批量处理

```php
$inputs = [
    new FileInput('path/to/image1.jpg'),
    new FileInput('path/to/image2.png')
];

$outputs = [
    new FileOutput('path/to/output1.jpg'),
    new FileOutput('path/to/output2.png')
];

$results = $compressor->batchCompress($inputs, $outputs);
```

## 错误处理

库使用异常机制处理错误。所有的异常都继承自`ImageCompressException`：

```php
try {
    $result = $compressor->compress($input, $output);
} catch (ImageCompressException $e) {
    echo "压缩失败：" . $e->getMessage();
}
```

## 示例

更多使用示例请查看`examples`目录。

## 许可证

MIT License