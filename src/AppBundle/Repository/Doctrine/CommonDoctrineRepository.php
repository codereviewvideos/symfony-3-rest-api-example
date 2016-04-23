<?php

namespace AppBundle\Repository\Doctrine;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Class CommonDoctrineRepository
 * @package AppBundle\Repository\Doctrine
 */
class CommonDoctrineRepository
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * DoctrineUserRepository constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @param mixed $object
     */
    public function refresh($object)
    {
        $this->em->refresh($object);
    }

    /**
     * @param   mixed               $object
     * @param   array               $arguments
     */
    public function save($object, array $arguments = ['flush'=>true])
    {
        $this->em->persist($object);

        if ($arguments['flush'] === true) {
            $this->em->flush();
        }
    }

    /**
     * @param   mixed               $object
     * @param   array               $arguments
     */
    public function delete($object, array $arguments = ['flush'=>true])
    {
        $this->em->remove($object);

        if ($arguments['flush'] === true) {
            $this->em->flush();
        }
    }
}
