<?php

namespace spec\AppBundle\Exception;

use AppBundle\Exception\HttpContentTypeException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class HttpContentTypeExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('AppBundle\Exception\HttpContentTypeException');
        $this->shouldHaveType('Symfony\Component\HttpKernel\Exception\HttpException');
    }

    function it_should_have_a_sensible_default_error_message()
    {
        $this->getMessage()->shouldReturn(HttpContentTypeException::ERROR_MESSAGE);
    }

    function it_should_have_the_correct_error_code()
    {
        $this->getStatusCode()->shouldReturn(HttpContentTypeException::ERROR_CODE);
    }
}