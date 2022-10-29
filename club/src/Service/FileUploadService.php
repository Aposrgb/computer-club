<?php

namespace App\Service;

use App\Helper\Exception\ApiException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploadService
{
    public function __construct(
        protected SluggerInterface $slugger,
        protected Filesystem       $fileSystem,
        protected string           $dirPublic
    )
    {
    }

    public function upload(UploadedFile $file, $targetDirectory): string
    {
        $fullTargetDirectory = $this->dirPublic . $targetDirectory;
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid('', true) . '.' . $file->guessExtension();
        $filePath = $targetDirectory . $fileName;
        try {
            if (!$this->fileSystem->exists($fullTargetDirectory)) {
                $this->fileSystem->mkdir($fullTargetDirectory);
            }
            $file->move($fullTargetDirectory, $fileName);
        } catch (FileException $e) {
            throw new ApiException('Ошибка при загрузке изображения на сервер', $e->getMessage());
        }

        return $filePath;
    }

    public function deleteFile($fileName): bool
    {
        $fullFilePath = $this->dirPublic . $fileName;
        if ($this->fileSystem->exists($fullFilePath)) {
            $this->fileSystem->remove($fullFilePath);
        }
        return true;
    }
}
