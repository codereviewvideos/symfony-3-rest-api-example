<?php

namespace AppBundle\Repository\Doctrine;

use AppBundle\Model\UserInterface;
use AppBundle\Repository\RepositoryInterface;
use AppBundle\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineUserRepository implements UserRepositoryInterface, RepositoryInterface
{
    /**
     * @var CommonDoctrineRepository
     */
    private $commonRepository;


    /**
     * DoctrineUserRepository constructor.
     * @param CommonDoctrineRepository $commonRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(CommonDoctrineRepository $commonRepository, EntityManagerInterface $em)
    {
        $this->commonRepository = $commonRepository;
        $this->em = $em;
    }

    /**
     * @param  UserInterface        $user
     */
    public function refresh(UserInterface $user)
    {
        $this->commonRepository->refresh($user);
    }

    /**
     * @param   UserInterface       $user
     * @param   array               $arguments
     */
    public function save(UserInterface $user, array $arguments = ['flush'=>true])
    {
        $this->commonRepository->save($user, $arguments);
    }

    /**
     * @param   UserInterface       $user
     * @param   array               $arguments
     */
    public function delete(UserInterface $user, array $arguments = ['flush'=>true])
    {
        $this->commonRepository->delete($user, $arguments);
    }

    public function findOneById($id)
    {
        return $this->em->getRepository('AppBundle:User')->find($id);
    }
}
