<?php

namespace Poplanchong123\ImageCompress\Output;

use Poplanchong123\ImageCompress\Exception\ImageCompressException;

class StreamOutput implements ImageOutputInterface
{
    public function save($resource, string $mimeType, array $options = []): string
    {
        $quality = $options['quality'] ?? 85;
        ob_start();
        $success = false;

        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                $success = imagejpeg($resource, null, $quality);
                break;
            case 'image/png':
                // PNG质量范围是0-9
                $pngQuality = (int)round((100 - $quality) * 9 / 100);
                $success = imagepng($resource, null, $pngQuality);
                break;
            case 'image/gif':
                $success = imagegif($resource);
                break;
            default:
                throw new ImageCompressException("Unsupported image type: {$mimeType}");
        }

        if (!$success) {
            ob_end_clean();
            throw new ImageCompressException("Failed to generate image stream");
        }

        $imageData = ob_get_clean();
        return $imageData;
    }
}