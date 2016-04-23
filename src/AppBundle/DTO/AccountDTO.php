<?php

namespace AppBundle\DTO;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class AccountDTO implements DTOInterface, SymfonyFormDTO
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var array
     * @Assert\Count(min="1", minMessage="This Account needs to be associated with at least one User ID")
     */
    private $users;

//    /**
//     * @var array
//     */
//    private $files;

    public function __construct()
    {
        $this->users = new ArrayCollection();
//        $this->files = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getDataClass()
    {
        return self::class;
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return [
            'name'                  => $this->name,
            'users'                 => $this->users,
//            'files'                 => $this->files,
        ];
    }

    /**
     * @return ArrayCollection<UserInterface>
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param Array<UserInterface> $users
     * @return $this
     */
    public function setUsers($users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

//    /**
//     * @return array
//     */
//    public function getFiles()
//    {
//        return $this->files;
//    }
//
//    /**
//     * @param array $files
//     * @return $this
//     */
//    public function setFiles($files)
//    {
//        $this->files = $files;
//
//        return $this;
//    }
}
