<?php

namespace AppBundle\Util;

/**
 * Class UploadFilesystem
 * @package AppBundle\Util
 */
class UploadFilesystem implements FilesystemInterface
{
    /**
     * @param $path
     * @return string
     */
    public function getFileContentsFromPath($path)
    {
        return file_get_contents($path);
    }
}