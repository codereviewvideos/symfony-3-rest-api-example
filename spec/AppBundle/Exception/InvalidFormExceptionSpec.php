<?php

namespace spec\AppBundle\Exception;

use AppBundle\Exception\InvalidFormException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InvalidFormExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('AppBundle\Exception\InvalidFormException');
    }

    function it_should_have_a_sensible_default_error_message()
    {
        $this->getMessage()->shouldReturn(InvalidFormException::DEFAULT_ERROR_MESSAGE);
    }

    function it_should_be_able_to_return_the_underlying_form()
    {
        $this->beConstructedWith(['a']);

        $this->getForm()->shouldReturn(['a']);
    }
}