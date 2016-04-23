<?php

namespace AppBundle\Repository\Doctrine;

use AppBundle\Entity\File;
use AppBundle\Entity\Repository\FileEntityRepository;
use AppBundle\Model\AccountInterface;
use AppBundle\Model\FileInterface;
use AppBundle\Model\UserInterface;
use AppBundle\Repository\FileRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class DoctrineFileRepository
 * @package AppBundle\Repository\Doctrine
 */
class DoctrineFileRepository implements FileRepositoryInterface
{
    /**
     * @var CommonDoctrineRepository
     */
    private $commonRepository;
    /**
     * @var FileEntityRepository
     */
    private $fileEntityRepository;

    /**
     * DoctrineFileRepository constructor.
     * @param CommonDoctrineRepository $commonRepository
     * @param FileEntityRepository $fileEntityRepository
     */
    public function __construct(CommonDoctrineRepository $commonRepository, FileEntityRepository $fileEntityRepository)
    {
        $this->commonRepository = $commonRepository;
        $this->fileEntityRepository = $fileEntityRepository;
    }

    /**
     * @param FileInterface         $file
     */
    public function refresh(FileInterface $file)
    {
        $this->commonRepository->refresh($file);
    }

    /**
     * @param   FileInterface       $file
     * @param   array               $arguments
     */
    public function save(FileInterface $file, array $arguments = ['flush'=>true])
    {
        $this->commonRepository->save($file, $arguments);
    }

    /**
     * @param   FileInterface       $file
     * @param   array               $arguments
     */
    public function delete(FileInterface $file, array $arguments = ['flush'=>true])
    {
        $this->commonRepository->delete($file, $arguments);
    }

    /**
     * @param   $id
     * @return  mixed
     */
    public function findOneById($id)
    {
        return $this->fileEntityRepository->find($id);
    }

    /**
     * @param   AccountInterface    $account
     * @return  array
     */
    public function findAllForAccount(AccountInterface $account)
    {
        return $this->fileEntityRepository->findAllForAccount($account);
    }

    /**
     * @param UserInterface $user
     * @return mixed
     */
    public function findAllForUser(UserInterface $user)
    {
        return $this->fileEntityRepository->findAllForUser($user);
    }
}