<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Model\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class AccountEntityRepository extends EntityRepository
{
    /**
     * @param   UserInterface       $user
     * @return  array
     */
    public function findAllForUser(UserInterface $user)
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('a')
            ->from('AppBundle\Entity\Account', 'a')
            ->join('a.users', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery();

        return $query->getResult();
    }
}