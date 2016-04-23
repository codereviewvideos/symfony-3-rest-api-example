<?php

namespace AppBundle\Handler;

use AppBundle\Form\Handler\FormHandlerInterface;
use AppBundle\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class UserHandler implements HandlerInterface
{
    /**
     * @var FormHandlerInterface
     */
    private $formHandler;
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    public function __construct(
        FormHandlerInterface $formHandler,
        UserRepositoryInterface $userRepositoryInterface
    )
    {
        $this->formHandler = $formHandler;
        $this->repository = $userRepositoryInterface;
    }

    public function get($id)
    {
        return $this->repository->findOneById($id);
    }

    public function all($limit = 10, $offset = 0)
    {
        throw new \DomainException('UserHandler::all is currently not implemented.');
    }

    public function post(array $parameters, array $options = [])
    {
        throw new \DomainException('UserHandler::post is currently not implemented.');
    }

    public function put($resource, array $parameters, array $options = [])
    {
        throw new \DomainException('UserHandler::put is currently not implemented.');
    }

    /**
     * @param UserInterface     $user
     * @param array             $parameters
     * @param array             $options
     * @return UserInterface
     */
    public function patch($user, array $parameters, array $options = [])
    {
        if ( ! $user instanceof UserInterface) {
            throw new \InvalidArgumentException('Not a valid User');
        }

        $user = $this->formHandler->handle(
            $user,
            $parameters,
            Request::METHOD_PATCH,
            $options
        );

        $this->repository->save($user);

        return $user;
    }

    public function delete($resource)
    {
        throw new \DomainException('UserHandler::delete is currently not implemented.');
    }

}