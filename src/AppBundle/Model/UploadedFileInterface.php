<?php

namespace AppBundle\Model;

/**
 * Interface UploadedFileInterface
 * @package AppBundle\Model
 */
interface UploadedFileInterface
{
    /**
     * @return string
     */
    public function getFilePath();

    /**
     * @return string
     */
    public function getOriginalFileName();

    /**
     * @return string
     */
    public function getFileExtension();

    /**
     * @return string
     */
    public function getMimeType();

    /**
     * @return int
     */
    public function getFileSize();
}