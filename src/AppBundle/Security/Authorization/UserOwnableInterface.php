<?php

namespace AppBundle\Security\Authorization;

use AppBundle\Model\UserInterface;

/**
 * Interface UserOwnableInterface
 * @package AppBundle\Security\Authorization
 */
interface UserOwnableInterface
{
    /**
     * @param UserInterface $user
     * @return bool
     */
    public function belongsToUser(UserInterface $user);
}