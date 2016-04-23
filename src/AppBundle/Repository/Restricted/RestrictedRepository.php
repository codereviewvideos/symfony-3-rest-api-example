<?php

namespace AppBundle\Repository\Restricted;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

abstract class RestrictedRepository
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * Throws an exception unless the attributes are granted against the current authentication token and optionally
     * supplied object.
     *
     * @param mixed  $attributes The attributes
     * @param mixed  $object     The object
     * @param string $message    The message passed to the exception
     *
     * @throws AccessDeniedHttpException
     */
    protected function denyAccessUnlessGranted($attributes, $object = null, $message = 'Access Denied.')
    {
        if (!$this->authorizationChecker->isGranted($attributes, $object)) {
            throw new AccessDeniedHttpException($message);
        }
    }
}