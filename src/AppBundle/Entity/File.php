<?php

namespace AppBundle\Entity;

use AppBundle\Model\FileInterface;
use AppBundle\Model\UserInterface;
use AppBundle\Model\AccountInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMSSerializer;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\FileEntityRepository")
 * @ORM\Table(name="file")
 * @JMSSerializer\ExclusionPolicy("all")
 */
class File implements FileInterface, \JsonSerializable
{
    /**
     * @ORM\Column(type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     * @JMSSerializer\Groups({"files_all","files_summary"})
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Account", mappedBy="files", cascade={"persist", "remove"})
     * @JMSSerializer\Groups({"files_all"})
     */
    private $accounts;

    /**
     * @ORM\Column(type="string", name="original_file_name")
     * @JMSSerializer\Expose
     * @JMSSerializer\SerializedName("originalFileName")
     * @JMSSerializer\Groups({"files_all","files_summary"})
     */
    private $originalFileName;

    /**
     * @ORM\Column(type="string", name="internal_file_name")
     * @JMSSerializer\Expose
     * @JMSSerializer\SerializedName("internalFileName")
     * @JMSSerializer\Groups({"files_all","files_summary"})
     */
    private $internalFileName;

    /**
     * @ORM\Column(type="string", name="guessed_extension")
     * @JMSSerializer\Expose
     * @JMSSerializer\SerializedName("guessedExtension")
     * @JMSSerializer\Groups({"files_all","files_summary"})
     */
    private $guessedExtension;

    /**
     * @ORM\Column(type="string", name="displayed_file_name")
     * @JMSSerializer\Expose
     * @JMSSerializer\SerializedName("displayedFileName")
     * @JMSSerializer\Groups({"files_all","files_summary"})
     */
    private $displayedFileName;

    /**
     * @ORM\Column(type="integer", name="file_size")
     * @JMSSerializer\Expose
     * @JMSSerializer\SerializedName("fileSize")
     * @JMSSerializer\Groups({"files_all","files_summary"})
     */
    private $fileSize;


    /**
     * File constructor.
     * @param $originalFileName
     * @param $internalFileName
     * @param $guessedExtension
     * @param $fileSize
     */
    public function __construct($originalFileName, $internalFileName, $guessedExtension, $fileSize)
    {
        $this->accounts = new ArrayCollection();

        $this->originalFileName = $originalFileName;
        $this->internalFileName = $internalFileName;
        $this->guessedExtension = $guessedExtension;
        $this->fileSize = $fileSize;

        $this->displayedFileName = $originalFileName;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getOriginalFileName()
    {
        return $this->originalFileName;
    }

    /**
     * @return string
     */
    public function getInternalFileName()
    {
        return $this->internalFileName;
    }

    /**
     * @return string
     */
    public function getGuessedExtension()
    {
        return $this->guessedExtension;
    }

    /**
     * @return mixed
     */
    public function getDisplayedFileName()
    {
        return $this->displayedFileName;
    }

    /**
     * @return File
     */
    public function changeDisplayedFileName($newName)
    {
        $this->displayedFileName = $newName;

        return $this;
    }

    /**
     * @param AccountInterface $account
     * @return File
     */
    public function addAccount(AccountInterface $account)
    {
        if ( ! $this->hasAccount($account)) {
            $this->accounts->add($account);
        }

        return $this;
    }

    /**
     * @param AccountInterface $account
     * @return File
     */
    public function removeAccount(AccountInterface $account)
    {
        if ($this->hasAccount($account)) {
            $this->accounts->removeElement($account);
        }

        return $this;
    }

    /**
     * @param AccountInterface $account
     * @return bool
     */
    public function hasAccount(AccountInterface $account)
    {
        return $this->accounts->contains($account);
    }

    /**
     * @return Collection
     */
    public function getAccounts()
    {
        return $this->accounts;
    }

    /**
     * @return int
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * @param UserInterface $user
     * @return bool
     */
    public function belongsToUser(UserInterface $user)
    {
        return $this
            ->getAccounts()
            ->filter(function ($account) use ($user) { /** @var $account AccountInterface */
                return $account->isManagedBy($user);
            })
            ->count() > 0
        ;
    }

    /**
     * @return string
     */
    function jsonSerialize()
    {
        return [
            'id'               => $this->id,
            'originalFileName' => $this->originalFileName,
            'internalFileName' => $this->internalFileName,
            'guessedExtension' => $this->guessedExtension,
            'fileSize'         => $this->fileSize,
        ];
    }

}