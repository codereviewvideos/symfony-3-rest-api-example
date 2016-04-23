<?php

namespace AppBundle\Form\Handler;

use AppBundle\Exception\InvalidFormException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;

class FormHandler implements FormHandlerInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FormTypeInterface
     */
    private $formType;

    /**
     * FormHandler constructor.
     * @param FormFactoryInterface $formFactory
     * @param FormTypeInterface $formType
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        FormTypeInterface $formType
    )
    {
        $this->formFactory = $formFactory;
        $this->formType = $formType;
    }

    /**
     * @param mixed  $object
     * @param array  $parameters
     * @param string $method
     * @param array  $options
     * @return mixed
     * @throws InvalidFormException
     */
    public function handle($object, array $parameters, $method, array $options = [])
    {
        $options = array_replace_recursive([
            'method'            => $method,
            'csrf_protection'   => false,
        ], $options);

        $form = $this->formFactory->create(get_class($this->formType), $object, $options);

        $form->submit($parameters, 'PATCH' !== $method);

        if (!$form->isValid()) {
            throw new InvalidFormException($form);
        }

        return $form->getData();
    }
}