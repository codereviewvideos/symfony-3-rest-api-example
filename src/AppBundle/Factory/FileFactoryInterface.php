<?php

namespace AppBundle\Factory;

use AppBundle\Model\FileInterface;
use AppBundle\Model\UploadedFileInterface;

interface FileFactoryInterface
{
    /**
     * @param string            $originalFileName
     * @param string            $internalFileName
     * @param string            $guessedExtension
     * @param int               $fileSize
     * @return FileInterface
     */
    public function create($originalFileName, $internalFileName, $guessedExtension, $fileSize);

    /**
     * @param  UploadedFileInterface      $uploadedFile
     * @return FileInterface
     */
    public function createFromUploadedFile(UploadedFileInterface $uploadedFile);
}