<?php

namespace AppBundle\Handler;

use AppBundle\DTO\FileDTO;
use AppBundle\Model\FileInterface;
use AppBundle\Repository\AccountRepositoryInterface;
use AppBundle\Util\UploadFilesystem;
use AppBundle\Model\AccountInterface;
use AppBundle\Factory\FileFactoryInterface;
use AppBundle\Form\Handler\FormHandlerInterface;
use AppBundle\Repository\FileRepositoryInterface;
use AppBundle\DataTransformer\FileDataTransformer;
use League\Flysystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class FileHandler implements FileHandlerInterface
{
    /**
     * @var AccountInterface
     */
    private $account;
    /**
     * @var FormHandlerInterface
     */
    private $formHandler;
    /**
     * @var FileDataTransformer
     */
    private $dataTransformer;
    /**
     * @var FileRepositoryInterface
     */
    private $repository;
    /**
     * @var FileFactoryInterface
     */
    private $factory;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var UploadFilesystem
     */
    private $uploadFilesystem;

    /**
     * FileHandler constructor.
     * @param FormHandlerInterface      $formHandler
     * @param FileDataTransformer       $dataTransformer
     * @param FileRepositoryInterface   $fileRepository
     * @param FileFactoryInterface      $fileFactory
     * @param Filesystem                $filesystem
     * @param UploadFilesystem          $uploadFilesystem
     */
    public function __construct(
        FormHandlerInterface $formHandler,
        FileDataTransformer $dataTransformer,
        FileRepositoryInterface $fileRepository,
        FileFactoryInterface $fileFactory,
        Filesystem $filesystem,
        UploadFilesystem $uploadFilesystem
    )
    {
        $this->formHandler = $formHandler;
        $this->repository = $fileRepository;
        $this->factory = $fileFactory;
        $this->dataTransformer = $dataTransformer;
        $this->filesystem = $filesystem;
        $this->uploadFilesystem = $uploadFilesystem;
    }

    /**
     * @param AccountInterface $account
     * @return $this
     */
    public function setAccount(AccountInterface $account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return AccountInterface
     */
    public function getAccount()
    {
        if ( empty($this->account) || ! $this->account instanceof AccountInterface) {
            throw new \BadMethodCallException('Unable to find a valid Account. Did you forget to set it?');
        }

        return $this->account;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function get($id)
    {
        return $this->repository->findOneById($id);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    public function all($limit = 10, $offset = 0)
    {
        return $this->repository->findAllForAccount($this->getAccount())->slice($offset, $limit);
    }

    /**
     * @param  array                 $parameters
     * @param  array                 $options
     * @return FileInterface
     */
    public function post(array $parameters, array $options = [])
    {
        $account = $this->getAccount();

        $options = array_replace_recursive([
            'validation_groups' => ['post'],
            'has_file'          => true,
        ], $options);

        $fileDTO = $this->formHandler->handle(
            new FileDTO(),
            $parameters,
            Request::METHOD_POST,
            $options
        ); /** @var $fileDTO FileDTO */

        $file = $this->factory->createFromUploadedFile($fileDTO->getUploadedFile());
        $file->changeDisplayedFileName($fileDTO->getName());

        $fileContents = $this->uploadFilesystem->getFileContentsFromPath($fileDTO->getUploadedFile()->getFilePath());
        $this->filesystem->put($file->getInternalFileName(), $fileContents);

        $account->addFile($file);

        $this->repository->save($file);

        return $file;
    }


    /**
     * @param  FileInterface        $file
     * @param  array                $parameters
     * @param  array                $options
     * @return mixed
     */
    public function patch($file, array $parameters, array $options = [])
    {
        $this->guardFileImplementsInterface($file);

        $options = array_replace_recursive([
            'validation_groups' => ['patch'],
            'has_file'          => false,
        ], $options);

        $fileDTO = $this->formHandler->handle(
            new FileDTO(),
            $parameters,
            Request::METHOD_PATCH,
            $options
        ); /** @var $fileDTO FileDTO */

        $this->repository->refresh($file);

        $file = $this->dataTransformer->updateFromDTO($file, $fileDTO);

        $this->repository->save($file);

        return $file;
    }


    /**
     * @param  FileInterface        $file
     * @param  array                $parameters
     * @param  array                $options
     * @return mixed
     */
    public function put($file, array $parameters, array $options = [])
    {
        $this->guardFileImplementsInterface($file);

        $options = array_replace_recursive([
            'validation_groups' => ['put'],
            'has_file'          => false,
        ], $options);

        $fileDTO = $this->formHandler->handle(
            new FileDTO(),
            $parameters,
            Request::METHOD_PUT,
            $options
        ); /** @var $fileDTO FileDTO */

        $this->repository->refresh($file);

        $file = $this->dataTransformer->updateFromDTO($file, $fileDTO);

        $this->repository->save($file);

        return $file;
    }


    /**
     * @param  FileInterface        $file
     * @return bool
     */
    public function delete($file)
    {
        $this->guardFileImplementsInterface($file);

        $isDeleted = $this->filesystem->delete($file->getInternalFileName());

        $this->repository->delete($file);

        return $isDeleted;
    }

    /**
     * This method is in place as class can't use a type hint due to interface
     * @param $file
     */
    private function guardFileImplementsInterface($file)
    {
        if (!$file instanceof FileInterface) {
            throw new \InvalidArgumentException('Expected passed File to implement FileInterface');
        }
    }
}