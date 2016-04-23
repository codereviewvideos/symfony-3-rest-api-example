<?php

namespace AppBundle\Repository\Restricted;

use AppBundle\Model\AccountInterface;
use AppBundle\Model\UserInterface;
use AppBundle\Repository\AccountRepositoryInterface;
use AppBundle\Repository\RepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class RestrictedAccountRepository
 * @package AppBundle\Repository\Restricted
 */
class RestrictedAccountRepository extends RestrictedRepository implements AccountRepositoryInterface, RepositoryInterface
{
    /**
     * @var AccountRepositoryInterface
     */
    private $repository;

    /**
     * RestrictedAccountRepository constructor.
     * @param AccountRepositoryInterface $repository
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        AccountRepositoryInterface $repository,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->repository = $repository;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param AccountInterface $account
     * @return mixed
     */
    public function refresh(AccountInterface $account)
    {
        $this->authorizationChecker->isGranted('view', $account);

        $this->repository->refresh($account);
    }

    /**
     * @param AccountInterface $account
     * @param array $arguments
     */
    public function save(AccountInterface $account, array $arguments = [])
    {
        $this->authorizationChecker->isGranted('view', $account);

        $this->repository->save($account);
    }

    /**
     * @param AccountInterface $account
     * @param array $arguments
     */
    public function delete(AccountInterface $account, array $arguments = [])
    {
        $this->authorizationChecker->isGranted('view', $account);

        $this->repository->delete($account);
    }

    /**
     * @param $id
     * @return mixed|null
     */
    public function findOneById($id)
    {
        $account = $this->repository->findOneById($id);

        $this->denyAccessUnlessGranted('view', $account);

        return $account;
    }

    /**
     * @param UserInterface $user
     * @return mixed
     */
    public function findAllForUser(UserInterface $user)
    {
        $accounts = $this->repository->findAllForUser($user);

        foreach ($accounts as $account) {
            $this->denyAccessUnlessGranted('view', $account);
        }

        return $accounts;
    }
}
