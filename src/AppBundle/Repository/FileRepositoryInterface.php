<?php

namespace AppBundle\Repository;

use AppBundle\Model\AccountInterface;
use AppBundle\Model\FileInterface;
use AppBundle\Model\UserInterface;

interface FileRepositoryInterface extends RepositoryInterface
{
    /**
     * @param FileInterface         $file
     * @return                      mixed
     */
    public function refresh(FileInterface $file);

    /**
     * @param FileInterface         $file
     * @param array                 $arguments
     */
    public function save(FileInterface $file, array $arguments = []);

    /**
     * @param FileInterface         $file
     * @param array                 $arguments
     */
    public function delete(FileInterface $file, array $arguments = []);

    /**
     * @param UserInterface         $user
     * @return                      mixed
     */
    public function findAllForUser(UserInterface $user);

    /**
     * @param AccountInterface      $account
     * @return                      mixed
     */
    public function findAllForAccount(AccountInterface $account);
}