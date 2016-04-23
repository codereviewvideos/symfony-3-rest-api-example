<?php

namespace spec\AppBundle\Entity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('AppBundle\Entity\User');
        $this->shouldImplement('AppBundle\Model\UserInterface');
    }
}
