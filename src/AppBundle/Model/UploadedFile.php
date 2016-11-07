<?php

namespace AppBundle\Model;

use AppBundle\Model\UploadedFileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

/**
 * Class SymfonyUploadedFile
 * @package AppBundle\Model
 */
class UploadedFile implements UploadedFileInterface
{
    private $filePath;
    private $originalFileName;
    private $mimeType;
    private $fileSize;
    private $fileExtension;

    /**
     * SymfonyUploadedFile constructor.
     * @param string $path
     * @param string $originalName
     * @param null $mimeType
     * @param null $size
     */
    private function __construct($path, $originalName, $mimeType, $size, $extension)
    {
        $this->filePath = $path;
        $this->originalFileName = $originalName;
        $this->mimeType = $mimeType;
        $this->fileSize = $size;
        $this->fileExtension = $extension;
    }

    /**
     * @param SymfonyUploadedFile $uploadedFile
     * @return SymfonyUploadedFile
     */
    public static function createFromSymfonyUploadedFile(SymfonyUploadedFile $uploadedFile)
    {
        return new UploadedFile(
            //$uploadedFile->getPath(),
            $uploadedFile->getPathname(),
            $uploadedFile->getClientOriginalName(),
            $uploadedFile->getClientMimeType(),
            $uploadedFile->getSize(),
            $uploadedFile->getClientOriginalExtension()
        );
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @return string
     */
    public function getOriginalFileName()
    {
        return $this->originalFileName;
    }

    /**
     * @return string
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @return int
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }
}
