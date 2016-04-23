<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class UserEntityRepository extends EntityRepository
{
    /**
     * @param       $user
     * @param array $options
     */
    public function save($user, $options = ['flush'=>true])
    {
        $this->_em->persist($user);

        if ($options['flush'] === true) {
            $this->_em->flush();
        }
    }
}