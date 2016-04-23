<?php

namespace AppBundle\DataTransformer;

use AppBundle\DTO\AccountDTO;
use AppBundle\Model\AccountInterface;
use AppBundle\Model\UserInterface;

class AccountDataTransformer
{
    public function convertToDTO(AccountInterface $account)
    {
        $dto = new AccountDTO();

        $dto->setName($account->getName());
        $dto->setUsers($account->getUsers());

        return $dto;
    }

    public function updateFromDTO(AccountInterface $account, AccountDTO $dto)
    {
        if ($account->getName() !== $dto->getName()) {
            $account->changeName($dto->getName());
        }

        $account->removeAllUsers();

        foreach ($dto->getUsers() as $user) { /** @var UserInterface $user */
            $user->addAccount($account);
        }

        return $account;
    }
}
