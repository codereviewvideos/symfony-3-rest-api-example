<?php

namespace AppBundle\DTO;

use AppBundle\Model\AccountInterface;
use AppBundle\Model\FileInterface;
use AppBundle\Model\UploadedFileInterface;
use AppBundle\Model\UserInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileDTO
 * @package AppBundle\DTO
 */
class FileDTO implements FileInterface, DTOInterface
{
    /**
     * @var string
     * @Assert\NotBlank(groups={"put","patch"})
     */
    private $name;

    /**
     * @var UploadedFile
     * @Assert\NotBlank(
     *     groups = { "post" },
     *     message = "A valid file is required"
     * )
     * @Assert\File(
     *     maxSize = "100M",
     *     groups = { "post" }
     * )
     */
    private $uploadedFile;

    /**
     * @return mixed
     */
    public function getDataClass()
    {
        return self::class;
    }

    /**
     * @return mixed
     */
    function jsonSerialize()
    {
        return [
            'name' => $this->name
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return UploadedFileInterface
     */
    public function getUploadedFile()
    {
        if (!$this->uploadedFile instanceof UploadedFile) {
            return null;
        }

        return \AppBundle\Model\UploadedFile::createFromSymfonyUploadedFile($this->uploadedFile);
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return $this
     */
    public function setUploadedFile(UploadedFile $uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        throw new \RuntimeException('Should never be calling this on a File DTO');
    }

    /**
     * @return mixed
     */
    public function getOriginalFileName()
    {
        throw new \RuntimeException('Should never be calling this on a File DTO');
    }

    /**
     * @return mixed
     */
    public function getInternalFileName()
    {
        throw new \RuntimeException('Should never be calling this on a File DTO');
    }

    /**
     * @return mixed
     */
    public function getGuessedExtension()
    {
        throw new \RuntimeException('Should never be calling this on a File DTO');
    }

    /**
     * @return mixed
     */
    public function getFileSize()
    {
        throw new \RuntimeException('Should never be calling this on a File DTO');
    }

    /**
     * @return mixed
     */
    public function getDisplayedFileName()
    {
        throw new \RuntimeException('Should never be calling this on a File DTO');
    }

    /**
     * @param $newName
     * @return mixed
     */
    public function changeDisplayedFileName($newName)
    {
        throw new \RuntimeException('Should never be calling this on a File DTO');
    }

    /**
     * @param AccountInterface $accountInterface
     * @return mixed
     */
    public function addAccount(AccountInterface $accountInterface)
    {
        throw new \RuntimeException('Should never be calling this on a File DTO');
    }

    /**
     * @param AccountInterface $accountInterface
     * @return mixed
     */
    public function removeAccount(AccountInterface $accountInterface)
    {
        throw new \RuntimeException('Should never be calling this on a File DTO');
    }

    /**
     * @param AccountInterface $accountInterface
     * @return mixed
     */
    public function hasAccount(AccountInterface $accountInterface)
    {
        throw new \RuntimeException('Should never be calling this on a File DTO');
    }

    /**
     * @return mixed
     */
    public function getAccounts()
    {
        throw new \RuntimeException('Should never be calling this on a File DTO');
    }

    /**
     * @param UserInterface $user
     * @return mixed
     */
    public function belongsToUser(UserInterface $user)
    {
        throw new \RuntimeException('Should never be calling this on a File DTO');
    }
}