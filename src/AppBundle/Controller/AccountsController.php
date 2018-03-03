<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use AppBundle\Entity\User;
use AppBundle\Exception\InvalidFormException;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AccountsController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Get a single account.
     *
     * @ApiDoc(
     *   output = "AppBundle\Entity\Account",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when not found"
     *   }
     * )
     *
     * @param int         $accountId    the account id
     *
     * @throws NotFoundHttpException when does not exist
     *
     * @Annotations\View(serializerGroups={
     *   "accounts_all",
     *   "users_summary"
     * })
     *
     * @return View
     */
    public function getAction($accountId)
    {
        return $this->getAccountRepository()->findOneById($accountId);
    }

    /**
     * Gets a collection of the given User's Accounts.
     *
     * @ApiDoc(
     *   output = "AppBundle\Entity\Account",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when not found"
     *   }
     * )
     *
     * @throws NotFoundHttpException when does not exist
     *
     * @Annotations\View(serializerGroups={
     *   "accounts_all",
     *   "users_summary"
     * })
     *
     * @return View
     */
    public function cgetAction()
    {
        $user = $this->getUser();

        return $this->getAccountRepository()->findAllForUser($user);
    }


    /**
     * Creates a new Account
     *
     * @ApiDoc(
     *  input = "AppBundle\Form\Type\AccountFormType",
     *  output = "AppBundle\Entity\Account",
     *  statusCodes={
     *         201="Returned when a new Account has been successfully created",
     *         400="Returned when the posted data is invalid"
     *     }
     * )
     *
     * @param Request $request
     * @return View
     */
    public function postAction(Request $request)
    {
        try {

            $account = $this->getHandler()->post($request->request->all());

            $routeOptions = [
                'accountId'  => $account->getId(),
                '_format'    => $request->get('_format'),
            ];

            return View::createRouteRedirect('get_accounts', $routeOptions, Response::HTTP_CREATED);

        } catch (InvalidFormException $e) {

            return $e->getForm();
        }
    }


    /**
     * Update existing Account from the submitted data
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "AppBundle\Form\AccountType",
     *   output = "AppBundle\Entity\Account",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when errors",
     *     404 = "Returned when not found"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the account id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when does not exist
     */
    public function patchAction(Request $request, $id)
    {
        $requestedAccount = $this->get('crv.repository.restricted_account_repository')->findOneById($id);

        try {

            $account = $this->getHandler()->patch(
                $requestedAccount,
                $request->request->all()
            );

            $routeOptions = [
                'accountId'  => $account->getId(),
                '_format'    => $request->get('_format'),
            ];

            return View::createRouteRedirect('get_accounts', $routeOptions, Response::HTTP_NO_CONTENT);

        } catch (InvalidFormException $e) {

            return $e->getForm();
        }
    }


    /**
     * Replaces existing Account from the submitted data
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "AppBundle\Form\AccountType",
     *   output = "AppBundle\Entity\Account",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when errors",
     *     404 = "Returned when not found"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the account id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when does not exist
     */
    public function putAction(Request $request, $id)
    {
        $requestedAccount = $this->get('crv.repository.restricted_account_repository')->findOneById($id);

        try {

            $account = $this->getHandler()->put(
                $requestedAccount,
                $request->request->all()
            );

            $routeOptions = [
                'accountId'  => $account->getId(),
                '_format'    => $request->get('_format'),
            ];

            return View::createRouteRedirect('get_accounts', $routeOptions, Response::HTTP_NO_CONTENT);

        } catch (InvalidFormException $e) {

            return $e->getForm();
        }
    }


    /**
     * Deletes a specific Account by ID
     *
     * @ApiDoc(
     *  description="Deletes an existing Account",
     *  statusCodes={
     *         204="Returned when an existing Account has been successfully deleted",
     *         403="Returned when trying to delete a non existent Account"
     *     }
     * )
     *
     * @param int         $id       the account id
     * @return View
     */
    public function deleteAction($id)
    {
        $requestedAccount = $this->get('crv.repository.restricted_account_repository')->findOneById($id);

        $this->getHandler()->delete($requestedAccount);

        return new View(null, Response::HTTP_NO_CONTENT);
    }


    /**
     * Returns the required handler for this controller
     *
     * @return \AppBundle\Handler\AccountHandler
     */
    private function getHandler()
    {
        return $this->get('crv.handler.restricted_account_handler');
    }

    /**
     * @return \AppBundle\Repository\Restricted\RestrictedAccountRepository
     */
    private function getAccountRepository()
    {
        return $this->get('crv.repository.restricted_account_repository');
    }
}