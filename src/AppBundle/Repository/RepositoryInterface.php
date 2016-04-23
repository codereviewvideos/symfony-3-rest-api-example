<?php

namespace AppBundle\Repository;

/**
 * Interface RepositoryInterface
 * @package AppBundle\Repository
 */
interface RepositoryInterface
{
    /**
     * @param                           $id
     * @return                          mixed|null
     */
    public function findOneById($id);
}