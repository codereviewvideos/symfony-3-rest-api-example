<?php

namespace AppBundle\Model;

use AppBundle\Entity\User;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\UserInterface as BaseUserInterface;

interface UserInterface extends BaseUserInterface
{
    /**
     * @param AccountInterface $accountInterface
     * @return User
     */
    public function addAccount(AccountInterface $accountInterface);

    /**
     * @param AccountInterface $accountInterface
     * @return User
     */
    public function removeAccount(AccountInterface $accountInterface);

    /**
     * @param AccountInterface $accountInterface
     * @return bool
     */
    public function hasAccount(AccountInterface $accountInterface);

    /**
     * @return Collection
     */
    public function getAccounts();
}