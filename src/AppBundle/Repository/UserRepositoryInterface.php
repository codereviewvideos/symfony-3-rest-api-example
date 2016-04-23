<?php

namespace AppBundle\Repository;
use AppBundle\Model\UserInterface;

/**
 * Interface UserRepositoryInterface
 * @package AppBundle\Repository
 */
interface UserRepositoryInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function findOneById($id);

    /**
     * @param UserInterface $user
     * @param array         $options
     * @return mixed
     */
    public function save(UserInterface $user, array $options = ['flush'=>true]);
}