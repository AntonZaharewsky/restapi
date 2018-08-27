<?php

namespace AppBundle\Controller;

use AppBundle\Services\UsersDataStore;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class UsersController
 * @package AppBundle\Controller
 */
class UsersController extends Controller
{
    const NOT_FOUND = 'Data not found in database';
    const SUCCESS = 'Operation was successfully completed.';

    /**
     * @var UsersDataStore
     */
    private $usersDataStore;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * UsersController constructor.
     * @param UsersDataStore      $usersDataStore
     * @param SerializerInterface $serializer
     */
    public function __construct(UsersDataStore $usersDataStore, SerializerInterface $serializer)
    {
        $this->usersDataStore = $usersDataStore;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/users/{id}", methods={"GET"})
     */
    public function getSingleAction($id)
    {
        $user = $this->usersDataStore->get($id);

        return new Response($this->serializer->serialize($user, 'json'), Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/users", methods={"GET"})
     */
    public function getAllAction()
    {
        $users = $this->usersDataStore->getAll();

        return new Response($this->serializer->serialize($users, 'json'), Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/users", methods={"POST"})
     */
    public function createAction(Request $request)
    {
        try {
            $this->usersDataStore->create($request);
        } catch (BadRequestHttpException $exception) {
            return new JsonResponse($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(self::SUCCESS, Response::HTTP_CREATED);
    }

    /**
     * @Route("/users/{id}", methods={"PATCH"})
     */
    public function updateAction($id, Request $request)
    {
        try {
            $this->usersDataStore->update($id, $request);
        } catch (NotFoundHttpException $exception) {
            return new JsonResponse(self::NOT_FOUND, Response::HTTP_NOT_FOUND);
        } catch (BadRequestHttpException $exception) {
            return new JsonResponse($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(self::SUCCESS, Response::HTTP_OK);
    }

    /**
     * @Route("/users/{id}", methods={"DELETE"})
     */
    public function deleteAction($id)
    {
        try {
            $this->usersDataStore->delete($id);
        } catch (NotFoundHttpException $e) {
            return new JsonResponse(self::NOT_FOUND, Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(self::SUCCESS, Response::HTTP_OK);
    }
}