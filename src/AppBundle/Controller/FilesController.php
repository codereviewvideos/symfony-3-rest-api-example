<?php

namespace AppBundle\Controller;

use AppBundle\Exception\InvalidFormException;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class FilesController
 * @package AppBundle\Controller
 * @Annotations\RouteResource("files")
 */
class FilesController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Get a single file.
     *
     * @Operation(
     *     tags={""},
     *     summary="Get a single file.",
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
     * @param int         $accountId        the Account id
     * @param int         $fileId           the File id
     *
     * @Annotations\View(serializerGroups={ "files_all", "accounts_summary" })
     *
     * @throws NotFoundHttpException when does not exist
     *
     * @return View
     */
    public function getAction($accountId, $fileId)
    {
        return $this->getFileHandler()->get($fileId);
    }

    /**
     * Gets a collection of the given User's Files.
     *
     * @Operation(
     *     tags={""},
     *     summary="Gets a collection of the given User's Files.",
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
     * @param int         $accountId        the Account id
     *
     * @Annotations\View(serializerGroups={ "files_all", "accounts_summary" })
     *
     * @throws NotFoundHttpException when does not exist
     *
     * @return View
     */
    public function cgetAction($accountId)
    {
        $account = $this->getAccountHandler()->get($accountId);

        return $this
            ->getFileHandler()
            ->setAccount($account)
            ->all()
        ;
    }


    /**
     * Creates a new File
     *
     * @Operation(
     *     tags={""},
     *     summary="Creates a new File",
     *     @SWG\Parameter(
     *         name="filePath",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="file"
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Returned when a new File has been successfully created"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when the posted data is invalid"
     *     )
     * )
     *
     *
     * @param Request     $request
     * @param int         $accountId    the account id
     *
     * @return View
     */
    public function postAction(Request $request, $accountId)
    {
        $this->getFileHandler()->setAccount(
            $this->getAccountHandler()->get($accountId)
        );

        $parameters = array_replace_recursive(
            $request->request->all(),
            $request->files->all()
        );

        try {
            $file = $this->getFileHandler()->post($parameters);
        } catch (InvalidFormException $e) {

            return $e->getForm();
        }

        $routeOptions = [
            'accountId'  => $accountId,
            'fileId'     => $file->getId(),
            '_format'    => $request->get('_format'),
        ];

        return $this->routeRedirectView('get_accounts_files', $routeOptions, Response::HTTP_CREATED);
    }


    /**
     * Update existing File from the submitted data
     *
     * @Operation(
     *     tags={""},
     *     summary="Update existing File from the submitted data",
     *     @SWG\Response(
     *         response="204",
     *         description="Returned when successful"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when errors"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when not found"
     *     )
     * )
     *
     *
     * @param Request $request      the request object
     * @param int     $accountId    the account id
     * @param int     $fileId       the file id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when does not exist
     */
    public function patchAction(Request $request, $accountId, $fileId)
    {
        $this->getFileHandler()->setAccount(
            $this->getAccountHandler()->get($accountId)
        );

        $file = $this->getFileHandler()->get($fileId);

        $parameters = array_replace_recursive(
            $request->request->all(),
            $request->files->all()
        );

        try {
            $file = $this->getFileHandler()->patch($file, $parameters);
        } catch (InvalidFormException $e) {

            return $e->getForm();
        }

        $routeOptions = [
            'accountId'  => $fileId,
            'fileId'     => $file->getId(),
            '_format'    => $request->get('_format'),
        ];

        return View::createRouteRedirect('get_accounts_files', $routeOptions, Response::HTTP_NO_CONTENT);
    }


    /**
     * Replaces existing File from the submitted data
     *
     * @Operation(
     *     tags={""},
     *     summary="Replaces existing File from the submitted data",
     *     @SWG\Response(
     *         response="204",
     *         description="Returned when successful"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when errors"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when not found"
     *     )
     * )
     *
     *
     * @param Request $request      the request object
     * @param int     $accountId    the account id
     * @param int     $fileId       the file id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when does not exist
     */
    public function putAction(Request $request, $accountId, $fileId)
    {
        $this->getFileHandler()->setAccount(
            $this->getAccountHandler()->get($accountId)
        );

        $file = $this->getFileHandler()->get($fileId);

        $parameters = array_replace_recursive(
            $request->request->all(),
            $request->files->all()
        );

        try {
            $file = $this->getFileHandler()->put($file, $parameters);
        } catch (InvalidFormException $e) {

            return $e->getForm();
        }

        $routeOptions = [
            'accountId'  => $fileId,
            'fileId'     => $file->getId(),
            '_format'    => $request->get('_format'),
        ];

        return View::createRouteRedirect('get_accounts_files', $routeOptions, Response::HTTP_NO_CONTENT);
    }


    /**
     * Deletes a specific File by ID
     *
     * @Operation(
     *     tags={""},
     *     summary="Deletes an existing File",
     *     @SWG\Response(
     *         response="204",
     *         description="Returned when an existing File has been successfully deleted"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Returned when trying to delete a non existent File"
     *     )
     * )
     *
     *
     * @param int     $accountId    the account id
     * @param int     $fileId       the file id
     *
     * @return View
     *
     * @throws NotFoundHttpException when does not exist
     */
    public function deleteAction($accountId, $fileId)
    {
        $file = $this->getFileHandler()->get($fileId);

        $this->getFileHandler()->delete($file);

        return new View(null, Response::HTTP_NO_CONTENT);
    }


    /**
     * @return \AppBundle\Handler\FileHandler
     */
    private function getFileHandler()
    {
        return $this->get('crv.handler.restricted_file_handler');
    }


    /**
     * @return \AppBundle\Handler\AccountHandler
     */
    private function getAccountHandler()
    {
        return $this->get('crv.handler.restricted_account_handler');
    }
}