<?php

namespace AppBundle\Security\Authorization\Voter;

use AppBundle\Model\AccountInterface;
use AppBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class AccountVoter
 * @package AppBundle\Security\Authorization\Voter
 */
class AccountVoter implements VoterInterface
{
    /**
     *
     */
    const VIEW = 'view';

    /**
     * @param string $attribute
     * @return bool
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::VIEW,
        ));
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        $supportedClass = 'AppBundle\Model\AccountInterface';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param TokenInterface            $token
     * @param null|AccountInterface     $requestedAccount
     * @param array                     $attributes
     * @return int
     */
    public function vote(TokenInterface $token, $requestedAccount, array $attributes)
    {
        // check if class of this object is supported by this voter
        if (!$this->supportsClass(get_class($requestedAccount))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // set the attribute to check against
        $attribute = $attributes[0];

        // check if the given attribute is covered by this voter
        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // get current logged in user
        $loggedInUser = $token->getUser(); /** @var $loggedInUser UserInterface */

        // make sure that this User has access to this account
        if ($loggedInUser->hasAccount($requestedAccount)) {
            return VoterInterface::ACCESS_GRANTED;
        }

        return VoterInterface::ACCESS_DENIED;
    }

}
