<?php

namespace spec\AppBundle\Handler;

use AppBundle\Entity\User;
use AppBundle\Form\Handler\FormHandlerInterface;
use AppBundle\Repository\UserRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;

class UserHandlerSpec extends ObjectBehavior
{
    private $repository;
    private $formHandler;

    function let(
        UserRepositoryInterface $repository,
        FormHandlerInterface $formHandler
    )
    {
        $this->formHandler = $formHandler;
        $this->repository = $repository;

        $this->beConstructedWith($repository, $formHandler);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('AppBundle\Handler\UserHandler');
        $this->shouldImplement('AppBundle\Handler\HandlerInterface');
    }

    function it_can_GET()
    {
        $id = 777;
        $this->get($id);
        $this->repository->find($id)->shouldHaveBeenCalled();
    }

    function it_cannot_get_ALL()
    {
        $this->shouldThrow('\DomainException')->during('all', [1,2]);
    }

    function it_cannot_POST()
    {
        $this->shouldThrow('\DomainException')->during('post', [['param1']]);
    }

    function it_cannot_PUT()
    {
        $this->shouldThrow('\DomainException')->during('put', [['param1'], []]);
    }

    function it_should_allow_PATCH(User $user)
    {
        $params = ['1','2','3'];

        $this->formHandler->handle($user, $params, Request::METHOD_PATCH, [])->willReturn($user);
        $this->repository->save(Argument::type('AppBundle\Entity\User'))->shouldBeCalled();

        $this->patch($user, $params)->shouldReturn($user);
    }

    function it_should_throw_if_PATCH_not_given_a_valid_user()
    {
        $this->shouldThrow('\InvalidArgumentException')->during('patch', ['not_user', []]);
        $this->shouldThrow('\InvalidArgumentException')->during('patch', [new \StdClass(), [123]]);
    }

    function it_cannot_DELETE()
    {
        $this->shouldThrow('\DomainException')->during('delete', ['something']);
    }
}
