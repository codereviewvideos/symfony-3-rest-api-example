<?php

namespace AppBundle\Factory;

use AppBundle\Entity\File;
use AppBundle\Model\UploadedFileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileFactory
 * @package AppBundle\Factory
 */
class FileFactory implements FileFactoryInterface
{
    /**
     * @param string $originalFileName
     * @param string $internalFileName
     * @param string $guessedExtension
     * @param int $fileSize
     * @return File
     */
    public function create($originalFileName, $internalFileName, $guessedExtension, $fileSize)
    {
        return new File($originalFileName, $internalFileName, $guessedExtension, $fileSize);
    }

    /**
     * @param UploadedFileInterface $uploadedFile
     * @return File
     */
    public function createFromUploadedFile(UploadedFileInterface $uploadedFile)
    {
        $internalFileName = sha1(uniqid(mt_rand(), true));

        return $this->create(
            $uploadedFile->getOriginalFileName(),
            $internalFileName,
            $uploadedFile->getFileExtension(),
            $uploadedFile->getFileSize()
        );
    }
}