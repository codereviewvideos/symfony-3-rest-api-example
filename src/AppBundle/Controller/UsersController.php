<?php

namespace AppBundle\Controller;

use AppBundle\Exception\InvalidFormException;
use AppBundle\Handler\UserHandler;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class UsersController
 * @package AppBundle\Controller
 * @Annotations\RouteResource("users")
 */
class UsersController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Get a single User.
     *
     * @Operation(
     *     tags={""},
     *     summary="Get a single User.",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when not found"
     *     )
     * )
     *
     *
     * @param int   $id     the user id
     *
     * @throws NotFoundHttpException when does not exist
     *
     * @return View
     */
    public function getAction($id)
    {
        $user = $this->getUserHandler()->get($id);

        return $this->view($user);
    }

    /**
     * Gets a collection of Users.
     *
     * @Operation(
     *     tags={""},
     *     summary="Gets a collection of Users.",
     *     @SWG\Response(
     *         response="405",
     *         description="Method not allowed"
     *     )
     * )
     *
     *
     * @throws MethodNotAllowedHttpException
     *
     * @return View
     */
    public function cgetAction()
    {
        throw new MethodNotAllowedHttpException([], "Method not allowed");
    }

    /**
     * Update existing User from the submitted data
     *
     * @Operation(
     *     tags={""},
     *     summary="Update existing User from the submitted data",
     *     @SWG\Response(
     *         response="204",
     *         description="Returned when successful"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when errors"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Returned when provided password is incorrect"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when not found"
     *     )
     * )
     *
     *
     * @param Request   $request    the request object
     * @param int       $id         the user id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when does not exist
     */
    public function patchAction(Request $request, $id)
    {
        $requestedUser = $this->get('crv.repository.restricted_user_repository')->findOneById($id);

        try {

            $statusCode = Response::HTTP_NO_CONTENT;

            /** @var $user \AppBundle\Entity\User */
            $user = $this->getUserHandler()->patch(
                $requestedUser,
                $request->request->all()
            );

            $routeOptions = array(
                'id'        => $user->getId(),
                '_format'   => $request->get('_format')
            );

            return View::createRouteRedirect('get_users', $routeOptions, $statusCode);

        } catch (InvalidFormException $e) {

            return $e->getForm();
        }
    }

    /**
     * @return UserHandler
     */
    private function getUserHandler()
    {
        return $this->container->get('crv.handler.restricted_user_handler');
    }
}