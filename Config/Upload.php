<?php

namespace Config;

use Exception;

class Upload
{
    const ALLOWED_TYPES = [
        'image/jpeg',
        'image/png',
        'application/pdf',
        'video/mp4',
        'video/quicktime',
    ];

    private $maxSize = 5242880;
    private $uploadPath;

    public function __construct(int $maxSize = null)
    {
        $this->uploadPath = rtrim(__DIR__, '/') . '../../files';

        if ($maxSize !== null) {
            $this->maxSize = $maxSize;
        }
    }

    public function uploadFile(array $file): string
    {
        $this->createUploadDirectory();

        if ($this->validateFile($file)) {
            $uploadedFilename = $this->generateUniqueFilename($file['name']);
            $destination = $this->uploadPath . '/' . $uploadedFilename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                return $uploadedFilename;
            } else {
                throw new Exception('Falha ao fazer o upload do arquivo.');
            }
        } else {
            throw new Exception('Arquivo inválido.');
        }
    }


    private function validateFile(array $file): bool
    {
        $allowedExtensions = [
            'jpg',
            'jpeg',
            'png',
            'pdf',
            'mp4',
            'mov',
            // Adicione aqui outras extensões permitidas, se necessário
        ];

        $filename = $file['name'];
        $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (empty($fileExtension) || !in_array($fileExtension, $allowedExtensions)) {
            return false;
        }

        $fileSize = $file['size'];

        if ($fileSize > $this->maxSize) {
            return false;
        }

        return true;
    }

    private function generateUniqueFilename(string $filename): string
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $basename = pathinfo($filename, PATHINFO_FILENAME);
        $newFilename = $basename . '_' . uniqid() . '.' . $extension;
        return $newFilename;
    }

    private function createUploadDirectory(): void
    {
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }
}
