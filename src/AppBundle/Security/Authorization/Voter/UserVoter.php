<?php

namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use AppBundle\Model\UserInterface;

class UserVoter implements VoterInterface
{
    const VIEW = 'view';

    public function supportsAttribute($attribute)
    {
        return self::VIEW == $attribute;
    }

    public function supportsClass($class)
    {
        $supportedClass = UserInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    public function vote(TokenInterface $token, $requestedUser, array $attributes)
    {
        if (null === $requestedUser) {
            return VoterInterface::ACCESS_DENIED;
        }

        // check if class of this object is supported by this voter
        if (!$this->supportsClass(get_class($requestedUser))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // set the attribute to check against
        $attribute = $attributes[0];

        // check if the given attribute is covered by this voter
        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // get current logged in user
        $loggedInUser = $token->getUser();

        // make sure there is a user object (i.e. that the user is logged in)
        if ($loggedInUser === $requestedUser) {
            return VoterInterface::ACCESS_GRANTED;
        }

        return VoterInterface::ACCESS_DENIED;
    }

}
