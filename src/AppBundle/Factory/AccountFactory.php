<?php

namespace AppBundle\Factory;

use AppBundle\DTO\AccountDTO;
use AppBundle\Entity\Account;

class AccountFactory implements AccountFactoryInterface
{
    /**
     * @param  string       $accountName
     * @return Account
     */
    public function create($accountName)
    {
        return new Account($accountName);
    }

    /**
     * @param  AccountDTO   $accountDTO
     * @return Account
     */
    public function createFromDTO(AccountDTO $accountDTO)
    {
        $account = self::create($accountDTO->getName());

        foreach ($accountDTO->getUsers() as $user) { /** @var $user \AppBundle\Model\UserInterface */
            $user->addAccount($account);
        }

        return $account;
    }
}
