<?php

namespace AppBundle\Event\Listener;

use AppBundle\Exception\HttpContentTypeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class ContentTypeListener
{
    const MIME_TYPE_APPLICATION_JSON = 'application/json';
    const MIME_TYPE_MULTIPART_FORM_DATA = 'multipart/form-data';

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($request->headers->contains('Content-type', self::MIME_TYPE_APPLICATION_JSON)) {
            return true;
        }

        if ($request->getMethod() === Request::METHOD_GET) {
            return true;
        }

        if ($this->isMultipartFilePost($request)) {
            return true;
        }

        throw new HttpContentTypeException();
    }

    private function isMultipartFilePost(Request $request)
    {
        $contentType = $request->headers->get('Content-type');

        $isMultipart = (strpos($contentType, self::MIME_TYPE_MULTIPART_FORM_DATA) !== false ? true : false);

        if ($isMultipart && $request->get('_route') === 'post_accounts_files') {
            return true;
        }

        return false;
    }
}
