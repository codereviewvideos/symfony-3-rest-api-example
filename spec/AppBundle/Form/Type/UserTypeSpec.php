<?php

namespace spec\AppBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UserTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(['Some\Form\Class']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('AppBundle\Form\Type\UserType');
        $this->shouldHaveType('FOS\UserBundle\Form\Type\ProfileFormType');
    }
}
