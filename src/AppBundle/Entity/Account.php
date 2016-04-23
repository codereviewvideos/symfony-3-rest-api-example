<?php

namespace AppBundle\Entity;

use AppBundle\Model\AccountInterface;
use AppBundle\Model\FileInterface;
use AppBundle\Model\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMSSerializer;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\AccountEntityRepository")
 * @ORM\Table(name="account")
 * @JMSSerializer\ExclusionPolicy("all")
 */
class Account implements AccountInterface, \JsonSerializable
{
    /**
     * @ORM\Column(type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     * @JMSSerializer\Groups({"accounts_all", "accounts_summary"})
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="accounts", cascade={"persist", "remove"})
     *
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("ArrayCollection<AppBundle\Entity\User>")
     * @JMSSerializer\MaxDepth(2)
     * @JMSSerializer\Groups({"accounts_all"})
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="File", inversedBy="accounts")
     * @ORM\JoinTable(name="accounts__files")
     *
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("array")
     **/
    private $files;

    /**
     * @ORM\Column(type="string", name="name")
     * @JMSSerializer\Expose
     * @JMSSerializer\Groups({"accounts_all", "accounts_summary"})
     */
    private $name;


    public function __construct($accountName)
    {
        $this->name = $accountName;
        $this->users = new ArrayCollection();
        $this->files = new ArrayCollection();
    }

    public function getName()
    {
        return $this->name;
    }

    public function changeName($newName)
    {
        $this->name = (string) $newName;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    public function addUsers(array $users)
    {
        foreach ($users as $user) {
            $this->addUser($user);
        }

        return $this;
    }

    public function addUser(UserInterface $user)
    {
        if ( ! $this->isManagedBy($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function isManagedBy(UserInterface $user)
    {
        return $this->users->contains($user);
    }

    public function removeUser(UserInterface $user)
    {
        if ($this->isManagedBy($user)) {
            $this->users->removeElement($user);
        }

        return $this;
    }

    public function removeAllUsers()
    {
        foreach ($this->users as $user) { /** @var UserInterface $user */
            $user->removeAccount($this);
            $this->removeUser($user);
        }
    }


    /**
     * @return ArrayCollection
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param array $files
     * @return $this
     */
    public function addFiles(array $files)
    {
        foreach ($files as $file) {
            $this->addFile($file);
        }

        return $this;
    }

    /**
     * @param FileInterface $file
     * @return $this
     */
    public function addFile(FileInterface $file)
    {
        if ( ! $this->usesFile($file)) {
            $file->addAccount($this);
            $this->files->add($file);
        }

        return $this;
    }

    /**
     * @param FileInterface $file
     * @return bool
     */
    public function usesFile(FileInterface $file)
    {
        return $this->files->contains($file);
    }

    /**
     * @param FileInterface $file
     * @return $this
     */
    public function removeFile(FileInterface $file)
    {
        if ($this->usesFile($file)) {
            $file->removeAccount($this);
            $this->files->removeElement($file);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeAllFiles()
    {
        foreach ($this->files as $file) { /** @var FileInterface $file */
            $this->removeFile($file);
        }

        return $this;
    }



    /**
     * @return mixed
     */
    function jsonSerialize()
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'users' => $this->users,
            'files' => $this->files,
        ];
    }

}
