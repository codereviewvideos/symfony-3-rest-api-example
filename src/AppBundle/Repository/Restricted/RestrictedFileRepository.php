<?php

namespace AppBundle\Repository\Restricted;

use AppBundle\Model\AccountInterface;
use AppBundle\Model\FileInterface;
use AppBundle\Model\UserInterface;
use AppBundle\Repository\FileRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class RestrictedFileRepository extends RestrictedRepository implements FileRepositoryInterface
{
    /**
     * @var FileRepositoryInterface
     */
    private $repository;

    /**
     * RestrictedAccountRepository constructor.
     * @param FileRepositoryInterface $repository
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        FileRepositoryInterface $repository,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->repository = $repository;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param FileInterface $file
     * @return mixed
     */
    public function refresh(FileInterface $file)
    {
        $this->denyAccessUnlessGranted('view', $file);

        $this->repository->refresh($file);
    }

    /**
     * @param FileInterface $file
     * @param array $arguments
     */
    public function save(FileInterface $file, array $arguments = ['flush'=>true])
    {
        $this->denyAccessUnlessGranted('view', $file);

        $this->repository->save($file, $arguments);
    }

    /**
     * @param FileInterface $file
     * @param array $arguments
     */
    public function delete(FileInterface $file, array $arguments = ['flush'=>true])
    {
        $this->denyAccessUnlessGranted('view', $file);

        $this->repository->delete($file, $arguments);
    }

    /**
     * @param $id
     * @return mixed|null
     */
    public function findOneById($id)
    {
        $file = $this->repository->findOneById($id);

        $this->denyAccessUnlessGranted('view', $file);

        return $file;
    }

    /**
     * @param AccountInterface $account
     * @return mixed
     */
    public function findAllForAccount(AccountInterface $account)
    {
        $files = $this->repository->findAllForAccount($account);

        foreach ($files as $file) {
            $this->denyAccessUnlessGranted('view', $file);
        }

        return $files;
    }

    /**
     * @param UserInterface $user
     * @return mixed
     */
    public function findAllForUser(UserInterface $user)
    {
        $files = $this->repository->findAllForUser($user);

        foreach ($files as $file) {
            $this->denyAccessUnlessGranted('view', $file);
        }

        return $files;
    }
}