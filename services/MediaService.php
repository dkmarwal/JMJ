<?php
/**
 * JMJ Enterprises Solutions - Secure Media & File Upload Service
 */

declare(strict_types=1);

namespace Services;

use Core\Database;
use Core\Auth;
use Exception;

class MediaService {
    private const ALLOWED_MIMES = [
        'image/jpeg'      => 'jpg',
        'image/png'       => 'png',
        'image/webp'      => 'webp',
        'image/svg+xml'   => 'svg',
        'application/pdf' => 'pdf'
    ];

    private const MAX_SIZE_BYTES = 10485760; // 10MB

    public static function upload(array $file, string $folder = 'general', ?string $altText = null): array {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new Exception('Invalid upload parameter.');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception(self::getUploadErrorMessage($file['error']));
        }

        if ($file['size'] > self::MAX_SIZE_BYTES) {
            throw new Exception('File size exceeds the 10MB maximum limit.');
        }

        // MIME validation
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if (!array_key_exists($mime, self::ALLOWED_MIMES)) {
            throw new Exception("Invalid file type: {$mime}. Only JPG, PNG, WEBP, SVG, and PDF files are permitted.");
        }

        $extension = self::ALLOWED_MIMES[$mime];
        $originalFilename = pathinfo($file['name'], PATHINFO_FILENAME);
        $safeBase = slugify($originalFilename);
        $uniqueFilename = $safeBase . '-' . time() . '-' . bin2hex(random_bytes(4)) . '.' . $extension;

        $targetDir = UPLOADS_PATH . '/' . trim($folder, '/');
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $targetPath = $targetDir . '/' . $uniqueFilename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception('Failed to save uploaded file on disk.');
        }

        $width = null;
        $height = null;
        if (str_starts_with($mime, 'image/') && $extension !== 'svg') {
            $imgSize = @getimagesize($targetPath);
            if ($imgSize) {
                $width = $imgSize[0];
                $height = $imgSize[1];
            }
        }

        $relativePath = trim($folder, '/') . '/' . $uniqueFilename;
        $userId = Auth::id() ?? 1;

        $db = Database::getInstance();
        $mediaId = $db->insert('media', [
            'user_id'           => $userId,
            'filename'          => $uniqueFilename,
            'original_filename' => $file['name'],
            'file_path'         => $relativePath,
            'file_type'         => $extension,
            'mime_type'         => $mime,
            'file_size'         => $file['size'],
            'width'             => $width,
            'height'            => $height,
            'alt_text'          => $altText ?: $originalFilename,
            'folder'            => $folder
        ]);

        AuditService::log("Uploaded media file: {$uniqueFilename}", 'media', $mediaId, 'UPLOAD');

        return [
            'id'                => $mediaId,
            'filename'          => $uniqueFilename,
            'path'              => $relativePath,
            'url'               => upload_url($relativePath),
            'original_filename' => $file['name'],
            'width'             => $width,
            'height'            => $height
        ];
    }

    private static function getUploadErrorMessage(int $code): string {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.',
            UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was selected for upload.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder on server.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
            default               => 'Unknown upload error.'
        };
    }
}
