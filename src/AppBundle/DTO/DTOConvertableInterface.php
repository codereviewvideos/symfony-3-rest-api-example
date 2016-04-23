<?php

namespace AppBundle\DTO;

/**
 * Interface DTOConvertableInterface
 * @package AppBundle\DTO
 */
interface DTOConvertableInterface
{
    /**
     * @return DTOInterface
     */
    public function convertToDTO();
}