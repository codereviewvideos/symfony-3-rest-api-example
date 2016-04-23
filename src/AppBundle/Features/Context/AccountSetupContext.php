<?php

namespace AppBundle\Features\Context;

use AppBundle\Entity\Account;
use AppBundle\Factory\AccountFactoryInterface;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserManagerInterface;

class AccountSetupContext implements Context, SnippetAcceptingContext
{
    use \Behat\Symfony2Extension\Context\KernelDictionary;

    /**
     * @var EntityManagerInterface
     */
    protected $em;
    /**
     * @var UserManagerInterface
     */
    protected $userManager;
    /**
     * @var AccountFactoryInterface
     */
    private $accountFactory;

    /**
     * AccountSetupContext constructor.
     * @param UserManagerInterface $userManager
     * @param EntityManagerInterface $em
     */
    public function __construct(
        UserManagerInterface $userManager,
        AccountFactoryInterface $accountFactory,
        EntityManagerInterface $em)
    {
        $this->userManager = $userManager;
        $this->accountFactory = $accountFactory;
        $this->em = $em;
    }

    /**
     * @Given there are Accounts with the following details:
     */
    public function thereAreAccountsWithTheFollowingDetails(TableNode $accounts)
    {
        foreach ($accounts->getColumnsHash() as $key => $val) {

            $account = $this->accountFactory->create($val['name']);

            $this->em->persist($account);
            $this->em->flush();


            $this->fixIdForAccountNamed($val['uid'], $val['name']);


            $account = $this->em->getRepository('AppBundle:Account')->find($val['uid']);

            $this->addUsersToAccount($val['users'], $account);

            if (isset($val['files'])) {
                $this->addFilesToAccount($val['files'], $account);
            }
        }

        $this->em->flush();
    }

    private function fixIdForAccountNamed($id, $accountName)
    {
        $qb = $this->em->createQueryBuilder();

        $query = $qb->update('AppBundle:Account', 'a')
            ->set('a.id', $qb->expr()->literal($id))
            ->where('a.name = :accountName')
            ->setParameters([
                'accountName' => $accountName,
            ])
            ->getQuery()
        ;

        $query->execute();
    }

    private function addUsersToAccount($userIds, Account $account)
    {
        $userIds = explode(',', $userIds);

        if (empty($userIds)) {
            return false;
        }

        foreach ($userIds as $userId) {
            /** @var $user \AppBundle\Entity\User */
            $user = $this->userManager->findUserBy(['id'=>$userId]);

            if (!$user) {
                continue;
            }

            $user->addAccount($account);
        }

        $this->em->flush();
    }

    private function addFilesToAccount($fileIds, Account $account)
    {
        if (empty($fileIds)) {
            return false;
        }

        $fileIds = explode(',', $fileIds);

        if (empty($fileIds)) {
            return false;
        }

        foreach ($fileIds as $fileId) {
            /** @var $file \AppBundle\Entity\File */
            $file = $this->em->getRepository('AppBundle:File')->find($fileId);

            if (!$file) {
                continue;
            }

            $account->addFile($file);
        }

        $this->em->flush();
    }
}