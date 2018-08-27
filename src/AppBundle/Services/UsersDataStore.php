<?php

namespace AppBundle\Services;

use AppBundle\Helpers\EntitiesValidator;
use AppBundle\Interfaces\DataSetterInterface;
use AppBundle\Interfaces\DataStoreInterface;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Users;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

/**
 * Class UsersDataStore
 * @package AppBundle\Services
 */
class UsersDataStore implements DataStoreInterface, DataSetterInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    private $userRepository;

    /**
     * @var EntitiesValidator
     */
    private $validator;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * UsersDataStore constructor.
     * @param EntityManagerInterface $entityManager
     * @param EntitiesValidator      $validator
     * @param SerializerInterface    $serializer
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EntitiesValidator $validator,
        SerializerInterface $serializer
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $this->entityManager->getRepository(Users::class);
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    /**
     * Get all users from database.
     *
     * @return array
     */
    public function getAll()
    {
        $users = $this->userRepository->findAll();

        return $users;
    }

    /**
     * Get single user from database.
     *
     * @param integer $id
     * @return object
     */
    public function get(int $id)
    {
        $user = $this->userRepository->find($id);

        return $user;
    }

    /**
     * @param $id
     * @return object
     */
    public function getUser($id)
    {
        $user = $this->userRepository->find($id);
        if (is_null($user)) {
            throw new NotFoundHttpException();
        }

        return $user;
    }

    /**
     * Update selected user.
     *
     * @param integer $id
     * @param Request $request
     * @return void
     */
    public function update(int $id, Request $request)
    {
        $user = $this->getUser($id);

        $username = $request->get('username');
        $email = $request->get('email');

        $user->setUsername($username)->setEmail($email);

        $validatorResponse = $this->validator->validate($user);

        if (!empty($validatorResponse)) {
            throw new BadRequestHttpException($this->serializer->serialize($validatorResponse, 'json'));
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * Create new user.
     *
     * @param Request $request
     * @return void
     */
    public function create(Request $request)
    {
        $username = $request->get('username');
        $email = $request->get('email');

        $user = new Users();

        $user->setEmail($email)->setUsername($username);

        $validatorResponse = $this->validator->validate($user);

        if (!empty($validatorResponse)) {
            throw new BadRequestHttpException($this->serializer->serialize($validatorResponse, 'json'));
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * Delete user.
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id)
    {
        $user = $this->getUser($id);

        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}