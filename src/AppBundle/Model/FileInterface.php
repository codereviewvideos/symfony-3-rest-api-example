<?php

namespace AppBundle\Model;

use AppBundle\Security\Authorization\UserOwnableInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface FileInterface
 * @package AppBundle\Model
 */
interface FileInterface extends UserOwnableInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @return string
     */
    public function getOriginalFileName();

    /**
     * @return string
     */
    public function getInternalFileName();

    /**
     * @return string
     */
    public function getGuessedExtension();

    /**
     * @return int
     */
    public function getFileSize();

    /**
     * @return string
     */
    public function getDisplayedFileName();

    /**
     * @return string
     */
    public function changeDisplayedFileName($newName);

    /**
     * @param AccountInterface $accountInterface
     * @return FileInterface
     */
    public function addAccount(AccountInterface $accountInterface);

    /**
     * @param AccountInterface $accountInterface
     * @return FileInterface
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