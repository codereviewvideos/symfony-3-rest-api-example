<?php

namespace AppBundle\Handler;

use AppBundle\DataTransformer\AccountDataTransformer;
use AppBundle\DTO\AccountDTO;
use AppBundle\DTO\DTOConvertableInterface;
use AppBundle\DTO\DTOUpdatableInterface;
use AppBundle\Factory\AccountFactoryInterface;
use AppBundle\Form\Handler\FormHandlerInterface;
use AppBundle\Model\AccountInterface;
use AppBundle\Model\UserInterface;
use AppBundle\Repository\AccountRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AccountHandler implements HandlerInterface
{
    /**
     * @var FormHandlerInterface
     */
    private $formHandler;
    /**
     * @var AccountDataTransformer
     */
    private $dataTransformer;
    /**
     * @var AccountRepositoryInterface
     */
    private $repository;
    /**
     * @var AccountFactoryInterface
     */
    private $factory;

    public function __construct(
        FormHandlerInterface $formHandler,
        AccountDataTransformer $dataTransformer,
        AccountRepositoryInterface $accountRepository,
        AccountFactoryInterface $accountFactory
    )
    {
        $this->formHandler = $formHandler;
        $this->repository = $accountRepository;
        $this->factory = $accountFactory;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function get($id)
    {
        if ($id === null) {
            throw new BadRequestHttpException('An account ID was not specified.');
        }

        return $this->repository->findOneById($id);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    public function all($limit = 10, $offset = 0)
    {
        if ($this->user === null || ! $this->user instanceof UserInterface) {
            throw new \BadMethodCallException('Unable to find a User, did you remember to set one?');
        }

        return $this->repository->findAllForUser($this->user)->slice($offset, $limit);
    }

    /**
     * @param array                 $parameters
     * @param array                 $options
     * @return AccountInterface
     */
    public function post(array $parameters, array $options = [])
    {
        $accountDTO = $this->formHandler->handle(
            new AccountDTO(),
            $parameters,
            Request::METHOD_POST,
            $options
        );

        $account = $this->factory->createFromDTO($accountDTO);

        $this->repository->save($account);

        return $account;
    }
    

    /**
     * @param  AccountInterface     $account
     * @param  array                $parameters
     * @param  array                $options
     * @return mixed
     */
    public function patch($account, array $parameters, array $options = [])
    {
        $this->guardAccountImplementsInterface($account);

        /** @var AccountInterface $account */
        $accountDTO = $this->dataTransformer->convertToDTO($account);

        $accountDTO = $this->formHandler->handle(
            $accountDTO,
            $parameters,
            Request::METHOD_PATCH,
            $options
        );

        $this->repository->refresh($account);

        $account = $this->dataTransformer->updateFromDTO($account, $accountDTO);

        $this->repository->save($account);

        return $account;
    }


    /**
     * @param  AccountInterface     $account
     * @param  array                $parameters
     * @param  array                $options
     * @return mixed
     */
    public function put($account, array $parameters, array $options = [])
    {
        $this->guardAccountImplementsInterface($account);

        /** @var AccountInterface $account */
        $accountDTO = $this->dataTransformer->convertToDTO($account);

        $accountDTO = $this->formHandler->handle(
            $accountDTO,
            $parameters,
            Request::METHOD_PUT,
            $options
        );

        $this->repository->refresh($account);

        $account = $this->dataTransformer->updateFromDTO($account, $accountDTO);

        $this->repository->save($account);

        return $account;
    }
    

    /**
     * @param mixed $resource
     * @return bool
     */
    public function delete($resource)
    {
        $this->guardAccountImplementsInterface($resource);

        $this->repository->delete($resource);

        return true;
    }

    /**
     * @param UserInterface $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param $account
     */
    private function guardAccountImplementsInterface($account)
    {
        if (!$account instanceof AccountInterface) {
            throw new \InvalidArgumentException('Expected passed Account to implement AccountInterface');
        }
    }
}
