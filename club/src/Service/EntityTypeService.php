<?php

namespace App\Service;

use App\Entity\EntityType;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EntityTypeService
{
    const COUNT_FILES_IN_TYPE = 5;
    const FILE_EXTENSION_AVAILABLE = [
        'image/jpg',
        'image/jpeg',
        'image/png',
        'video/mp4',
    ];

    const PATH_TO_SAVE_FILES = "/uploads/typeFiles/";

    public function __construct(
        protected FileUploadService $fileUploadService
    )
    {
    }

    /** @param UploadedFile[] $files */
    public function uploadFiles(EntityType $entityType, array $files): EntityType
    {
        foreach ($files as $file) {
            $entityType->addFile(
                $this->fileUploadService->upload($file, self::PATH_TO_SAVE_FILES)
            );
        }
        return $entityType;
    }
}