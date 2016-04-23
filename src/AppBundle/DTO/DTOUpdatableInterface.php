<?php

namespace AppBundle\DTO;

/**
 * Interface DTOUpdatableInterface
 * @package AppBundle\DTO
 */
interface DTOUpdatableInterface
{
    /**
     * @param $object
     * @return mixed
     */
    public function updateFromDTO($object);
}