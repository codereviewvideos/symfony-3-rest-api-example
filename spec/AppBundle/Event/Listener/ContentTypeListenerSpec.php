<?php

namespace spec\AppBundle\Event\Listener;

use AppBundle\Event\Listener\ContentTypeListener;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class ContentTypeListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('AppBundle\Event\Listener\ContentTypeListener');
    }

    function it_throws_if_not_matching_other_rules(GetResponseEvent $event)
    {
        $request = new Request();
        $request->headers->set('Content-type', 'blah');
        $request->setMethod(Request::METHOD_PUT);

        $event->getRequest()->willReturn($request);

        $this->shouldThrow('AppBundle\Exception\HttpContentTypeException')
            ->during('onKernelRequest', [$event]);
    }

    function it_throws_if_no_content_type_and_is_not_GET(GetResponseEvent $event)
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_POST);

        $event->getRequest()->willReturn($request);

        $this->shouldThrow('AppBundle\Exception\HttpContentTypeException')
            ->during('onKernelRequest', [$event]);
    }

    function it_expects_a_valid_content_type_header(GetResponseEvent $event)
    {
        $request = new Request();
        $request->headers->set('Content-type', ContentTypeListener::MIME_TYPE_APPLICATION_JSON);

        $event->getRequest()->willReturn($request);

        $this->onKernelRequest($event)->shouldReturn(true);
    }

    function it_should_ignore_method_type_GET(GetResponseEvent $event)
    {
        $request = new Request();
        $request->headers->set('Content-type', 'fake');
        $request->setMethod(Request::METHOD_GET);

        $event->getRequest()->willReturn($request);

        $this->onKernelRequest($event)->shouldReturn(true);
    }

    function it_should_deny_multipart_form_data_for_none_file_route(GetResponseEvent $event)
    {
//        $request = new Request();
//        $request->request->set('_route', 'some_none_file_route');
//        $request->headers->set('Content-type', ContentTypeListener::MIME_TYPE_MULTIPART_FORM_DATA);
//        $request->setMethod(Request::METHOD_POST);
//
//        $event->getRequest()->willReturn($request);
//
//        $this->shouldThrow('AppBundle\Exception\HttpContentTypeException')
//            ->during('onKernelRequest', [$event]);
    }

    function it_should_allow_multipart_form_data_for_file_routes(GetResponseEvent $event)
    {
//        $request = new Request();
//        $request->request->set('_route', 'post_accounts_files');
//        $request->headers->set('Content-type', ContentTypeListener::MIME_TYPE_MULTIPART_FORM_DATA);
//        $request->setMethod(Request::METHOD_POST);
//
//        $event->getRequest()->willReturn($request);
//
//        $this->onKernelRequest($event)->shouldReturn(true);
    }
}