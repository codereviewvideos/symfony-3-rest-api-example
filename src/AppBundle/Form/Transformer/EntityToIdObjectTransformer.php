<?php

namespace AppBundle\Form\Transformer;

use AppBundle\Repository\RepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class EntityToIdObjectTransformer
 * @package AppBundle\Form\Transformer
 */
class EntityToIdObjectTransformer implements DataTransformerInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * EntityToIdObjectTransformer constructor.
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Do nothing.
     *
     * @param object|null $object
     *
     * @return string
     */
    public function transform($object)
    {
        return '';
    }

    /**
     * Transforms an array including an identifier to an object.
     *
     * @param array $idObject
     *
     * @return object|null
     *
     * @throws TransformationFailedException if object is not found.
     */
    public function reverseTransform($idObject)
    {
        if (!is_array($idObject)) {
            return false;
        }

        if ( ! array_key_exists('id', $idObject)) {
            throw new TransformationFailedException('Unable to find an ID key / value pair on passed in $idObject');
        }

        $object = $this->repository->findOneById($idObject['id']);

        if (null === $object) {
            throw new TransformationFailedException(sprintf(
                'A "%s" with ID "%s" does not exist!',
                get_class($object),
                $idObject['id']
            ));
        }

        return $object;
    }
}