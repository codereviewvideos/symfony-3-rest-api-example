<?php

namespace AppBundle\Repository\Doctrine;

use AppBundle\Model\UserInterface;
use AppBundle\Model\AccountInterface;
use AppBundle\Repository\AccountRepositoryInterface;
use AppBundle\Entity\Repository\AccountEntityRepository;
use AppBundle\Repository\RepositoryInterface;

/**
 * Class DoctrineAccountRepository
 * @package AppBundle\Repository\Doctrine
 */
class DoctrineAccountRepository implements AccountRepositoryInterface, RepositoryInterface
{
    /**
     * @var CommonDoctrineRepository
     */
    private $commonRepository;
    /**
     * @var AccountEntityRepository
     */
    private $accountEntityRepository;

    /**
     * DoctrineUserRepository constructor.
     * @param   CommonDoctrineRepository    $commonRepository
     * @param   AccountEntityRepository     $accountEntityRepository
     */
    public function __construct(CommonDoctrineRepository $commonRepository, AccountEntityRepository $accountEntityRepository)
    {
        $this->commonRepository = $commonRepository;
        $this->accountEntityRepository = $accountEntityRepository;
    }

    /**
     * @param AccountInterface $account
     */
    public function refresh(AccountInterface $account)
    {
        $this->commonRepository->refresh($account);
    }

    /**
     * @param   AccountInterface    $account
     * @param   array               $arguments
     */
    public function save(AccountInterface $account, array $arguments = ['flush'=>true])
    {
        $this->commonRepository->save($account, $arguments);
    }

    /**
     * @param   AccountInterface    $account
     * @param   array               $arguments
     */
    public function delete(AccountInterface $account, array $arguments = ['flush'=>true])
    {
        $this->commonRepository->delete($account, $arguments);
    }

    /**
     * @param   $id
     * @return  mixed
     */
    public function findOneById($id)
    {
        return $this->accountEntityRepository->find($id);
    }

    /**
     * @param   UserInterface       $user
     * @return  array
     */
    public function findAllForUser(UserInterface $user)
    {
        return $this->accountEntityRepository->findAllForUser($user);
    }
}
