<?php

namespace AppBundle\Model;

/**
 * Interface AccountInterface
 * @package AppBundle\Model
 */
interface AccountInterface
{
    /**
     * @return int|null
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $newName
     * @return AccountInterface
     */
    public function changeName($newName);

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers();

    /**
     * @param UserInterface $user
     * @return AccountInterface
     */
    public function addUser(UserInterface $user);

    /**
     * @param UserInterface $user
     * @return bool
     */
    public function isManagedBy(UserInterface $user);

    /**
     * @param UserInterface $user
     * @return 
     */
    public function removeUser(UserInterface $user);

    /**
     * @return void
     */
    public function removeAllUsers();

//    /**
//     * @return \Doctrine\Common\Collections\Collection
//     */
//    public function getFiles();
//
//    /**
//     * @param FileInterface $file
//     * @return AccountInterface
//     */
//    public function addFile(FileInterface $file);
//
//    /**
//     * @param FileInterface $file
//     * @return bool
//     */
//    public function usesFile(FileInterface $file);
//
//    /**
//     * @param FileInterface $file
//     * @return
//     */
//    public function removeFile(FileInterface $file);
//
//    /**
//     * @return void
//     */
//    public function removeAllFiles();
}
