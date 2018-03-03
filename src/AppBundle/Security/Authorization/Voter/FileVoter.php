<?php

namespace AppBundle\Security\Authorization\Voter;

use AppBundle\Model\AccountInterface;
use AppBundle\Model\FileInterface;
use AppBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class FileVoter implements VoterInterface
{
    const VIEW = 'view';

    /**
     * @param string $attribute
     * @return bool
     */
    public function supportsAttribute($attribute)
    {
        return self::VIEW === $attribute;
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        $supportedClass = FileInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param TokenInterface            $token
     * @param null|FileInterface        $requestedFile
     * @param array                     $attributes
     * @return int
     */
    public function vote(TokenInterface $token, $requestedFile, array $attributes)
    {
        if (null === $requestedFile) {
            return VoterInterface::ACCESS_DENIED;
        }

        // check if class of this object is supported by this voter
        if (!$this->supportsClass(get_class($requestedFile))) {
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

        try {
            if ($requestedFile->belongsToUser($loggedInUser)) {

                return VoterInterface::ACCESS_GRANTED;
            }
        } catch (\Exception $e) {

            return VoterInterface::ACCESS_DENIED;
        }

        return VoterInterface::ACCESS_DENIED;
    }

}