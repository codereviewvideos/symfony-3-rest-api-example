<?php

namespace AppBundle\Handler;

use AppBundle\Model\AccountInterface;

/**
 * Interface FileHandlerInterface
 * @package AppBundle\Handler
 */
interface FileHandlerInterface extends HandlerInterface
{
    /**
     * @param AccountInterface $account
     * @return void
     */
    public function setAccount(AccountInterface $account);

    /**
     * @return AccountInterface
     */
    public function getAccount();
}