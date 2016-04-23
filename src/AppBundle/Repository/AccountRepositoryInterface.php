<?php

namespace AppBundle\Repository;

use AppBundle\Model\AccountInterface;
use AppBundle\Model\UserInterface;

/**
 * Interface AccountRepositoryInterface
 * @package AppBundle\Repository
 */
interface AccountRepositoryInterface
{
    /**
     * @param AccountInterface $account
     * @return mixed
     */
    public function refresh(AccountInterface $account);

    /**
     * @param AccountInterface      $account
     * @param array                 $arguments
     */
    public function save(AccountInterface $account, array $arguments = []);

    /**
     * @param AccountInterface      $account
     * @param array                 $arguments
     */
    public function delete(AccountInterface $account, array $arguments = []);

    /**
     * @param                       $id
     * @return                      mixed|null
     */
    public function findOneById($id);

    /**
     * @param UserInterface         $user
     * @return mixed
     */
    public function findAllForUser(UserInterface $user);
}