<?php

namespace spec\AppBundle\Repository\Doctrine;

use AppBundle\Entity\Repository\UserEntityRepository;
use AppBundle\Model\UserInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UserRepositorySpec extends ObjectBehavior
{
    private $entityRepository;

    function let(UserEntityRepository $entityRepository)
    {
        $this->entityRepository = $entityRepository;

        $this->beConstructedWith($entityRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('AppBundle\Repository\Doctrine\UserRepository');
        $this->shouldImplement('AppBundle\Repository\UserRepositoryInterface');
    }

    function it_can_find()
    {
        $id = 24234;
        $this->find($id);
        $this->entityRepository->find($id)->shouldHaveBeenCalled();
    }

    function it_can_save_a_user_to_the_repository(UserInterface $user)
    {
        $this->save($user);
        $this->entityRepository->save($user, ['flush'=>true])->shouldHaveBeenCalled();
    }

    function it_can_save_without_flushing(UserInterface $user)
    {
        $this->save($user, ['flush'=>false]);
        $this->entityRepository->save($user, ['flush'=>false])->shouldHaveBeenCalled();
    }
}
