<?php

namespace spec\AppBundle\Form\Handler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;

class FormHandlerSpec extends ObjectBehavior
{
    /**
     * @var FormInterface
     */
    private $form;

    function let(
        FormFactoryInterface $formFactory,
        FormTypeInterface $formType,
        FormInterface $form
    )
    {
        $this->form = $form;

        $formFactory->create(Argument::any(), Argument::any(), Argument::any())->willReturn($this->form);

        $this->beConstructedWith($formFactory, $formType);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('AppBundle\Form\Handler\FormHandler');
        $this->shouldImplement('AppBundle\Form\Handler\FormHandlerInterface');
    }

    function it_should_throw_if_form_is_invalid()
    {
        $this->form->submit(Argument::any(), Argument::any())->willReturn(false);
        $this->form->isValid()->willReturn(false);

        $this
            ->shouldThrow('AppBundle\Exception\InvalidFormException')
            ->during('handle', [new \StdClass(), [1,2,3], 'BAD', []])
        ;
    }

    function it_can_handle_a_form()
    {
        $this->form->submit(Argument::any(), Argument::any())->shouldBeCalled();
        $this->form->isValid()->willReturn(true);
        $this->form->getData()->shouldBeCalled();

        $this->handle(new \StdClass(), [1,2,3], 'FAKE', []);
    }
}