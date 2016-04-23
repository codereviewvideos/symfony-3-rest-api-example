<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Model\AccountInterface;
use AppBundle\Model\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class FileEntityRepository extends EntityRepository
{
    /**
     * @param   AccountInterface       $account
     * @return  array
     */
    public function findAllForAccount(AccountInterface $account)
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('f')
            ->from('AppBundle\Entity\File', 'f')
            ->join('f.accounts', 'a')
            ->where('a.id = :accountId')
            ->setParameter('accountId', $account->getId())
            ->getQuery();

        return new ArrayCollection(
            $query->getResult()
        );
    }

    /**
     * @param   UserInterface       $user
     * @return  array
     */
    public function findAllForUser(UserInterface $user)
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('f')
            ->from('AppBundle\Entity\File', 'f')
            ->join('f.accounts', 'a')
            ->join('a.users', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery();

        return new ArrayCollection(
            $query->getResult()
        );
    }
}