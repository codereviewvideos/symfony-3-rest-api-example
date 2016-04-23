<?php

namespace AppBundle\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit_Framework_Assert as Assertions;

class MysqlDatabaseContext implements Context, SnippetAcceptingContext
{
    use \Behat\Symfony2Extension\Context\KernelDictionary;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * MysqlDatabaseContext constructor.
     * @param EntityManagerInterface        $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Then the :entity with id: :id should have been deleted
     */
    public function theWithIdXShouldHaveBeenDeleted($entity, $id)
    {
        $className = $this->getClassNameFor($entity);

        Assertions::isNull(
            $this->em->getRepository($className)->find($id)
        );
    }

    /**
     * @param $entity
     * @return string
     */
    private function getClassNameFor($entity)
    {
        switch ($entity) {

            case 'Account':
                $entityPath = 'AppBundle:Account';
                break;

            case 'File':
                $entityPath = 'AppBundle:File';
                break;

            default:
                throw new \InvalidArgumentException(
                    sprintf('Unrecognised Entity: %s, did you forget to add a new class name to the switch statement?',
                        $entity
                    )
                );
        }

        return $entityPath;
    }
}