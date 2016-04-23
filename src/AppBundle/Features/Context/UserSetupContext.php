<?php

namespace AppBundle\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use PhpSpec\Exception\Example\PendingException;

class UserSetupContext implements Context, SnippetAcceptingContext
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(UserManagerInterface $userManager, EntityManagerInterface $em)
    {
        $this->userManager = $userManager;
        $this->em = $em;
    }

    /**
     * @Given there are Users with the following details:
     */
    public function thereAreUsersWithTheFollowingDetails(TableNode $users)
    {
        foreach ($users->getColumnsHash() as $key => $val) {
            
            $user = $this->userManager->createUser();

            $user->setEnabled(true);
            $user->setUsername($val['username']);
            $user->setEmail($val['email']);
            $user->setPlainPassword($val['password']);

            $this->userManager->updateUser($user);


            $qb = $this->em->createQueryBuilder();

            $query = $qb->update('AppBundle:User', 'u')
                ->set('u.id', $qb->expr()->literal($val['uid']))
                ->where('u.username = :username')
                ->andWhere('u.email = :email')
                ->setParameters([
                    'username' => $val['username'],
                    'email'    => $val['email']
                ])
                ->getQuery();

            $query->execute();
        }
    }
}