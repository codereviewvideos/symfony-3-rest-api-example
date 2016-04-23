<?php

namespace spec\AppBundle\Security\Authorisation\Voter;

use AppBundle\Entity\User;
use AppBundle\Security\Authorisation\Voter\UserVoter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserVoterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('AppBundle\Security\Authorisation\Voter\UserVoter');
        $this->shouldImplement('Symfony\Component\Security\Core\Authorization\Voter\VoterInterface');
    }

    function it_supports_expected_attributes()
    {
        $this->supportsAttribute('view')->shouldReturn(true);
    }

    function it_doesnt_support_unexpected_attributes()
    {
        $this->supportsAttribute('1')->shouldReturn(false);
    }

    function it_supports_expected_class(User $user)
    {
        $this->supportsClass($user)->shouldReturn(true);
    }

    function it_doesnt_support_unexpected_class()
    {
        $this->supportsClass(new \StdClass())->shouldReturn(false);
    }

    function it_abstains_if_doesnt_support_attribute(TokenInterface $token, User $user)
    {
        $this->vote($token, $user, ['unsupported'])->shouldReturn(VoterInterface::ACCESS_ABSTAIN);
    }

    function it_abstains_if_doesnt_support_this_class(TokenInterface $token)
    {
        $this->vote($token, new \StdClass(), [UserVoter::VIEW])->shouldReturn(VoterInterface::ACCESS_ABSTAIN);
    }

    function it_denies_if_not_matching(TokenInterface $token, User $user1, User $user2)
    {
        $token->getUser()->willReturn($user2);

        $this->vote($token, $user1, [UserVoter::VIEW])->shouldReturn(VoterInterface::ACCESS_DENIED);
    }

    function it_grants_if_matching(TokenInterface $token, User $user)
    {
        $token->getUser()->willReturn($user);

        $this->vote($token, $user, [UserVoter::VIEW])->shouldReturn(VoterInterface::ACCESS_GRANTED);
    }
}