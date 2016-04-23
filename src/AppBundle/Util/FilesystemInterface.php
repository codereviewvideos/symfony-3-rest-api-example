<?php

namespace AppBundle\Util;

/**
 * Interface FilesystemInterface
 * @package AppBundle\Util
 */
interface FilesystemInterface
{
    /**
     * @return string|bool
     */
    public function getFileContentsFromPath($path);
}