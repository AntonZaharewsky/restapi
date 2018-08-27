<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Users;
use AppBundle\Helpers\EntitiesValidator;
use AppBundle\Services\UsersDataStore;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UsersControllerTest extends TestCase
 * @package Tests\AppBundle\Controller
 */
class UsersControllerTest extends TestCase
{
    /**
     * @var EntityRepository Repository mock.
     */
    private $repositoryMock;

    /**
     * @var EntitiesValidator Entities validator mock.
     */
    private $validatorMock;

    /**
     * @var EntityManager Entity manager mock.
     */
    private $entityManagerMock;

    /**
     * @var UsersDataStore Users data store mock.
     */
    private $usersDataStore;

    /**
     * Test setUp method.
     */
    public function setUp()
    {
        $this->repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->validatorMock = $this->getMockBuilder(EntitiesValidator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($this->repositoryMock));

        $this->usersDataStore = new UsersDataStore($this->entityManagerMock, $this->validatorMock);
    }

    /**
     * Test getAll.
     *
     * @return void
     *
     * @throws \Doctrine\ORM\ORMException Default doctrine exception.
     */
    public function testGetAll()
    {
        $this->repositoryMock->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue([new Users(), new Users()]));

        $response = $this->usersDataStore->getAll();

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * Test get.
     *
     * @dataProvider providerTestGet
     *
     * @param integer    $id
     * @param Users|null $expectedUser
     *
     * @return void
     *
     * @throws \Doctrine\ORM\ORMException Default doctrine exception.
     */
    public function testGet($id, $expectedUser)
    {
        $this->repositoryMock->expects($this->once())
            ->method('find')
            ->with($id)
            ->will($this->returnValue($expectedUser));

        $response = $this->usersDataStore->get($id);

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * Provider for testGet.
     *
     * @return array
     */
    public function providerTestGet()
    {
        return [
            [1, new Users()],
            [-1, null]
        ];
    }

    /**
     * Test update.
     *
     * @dataProvider providerTestUpdate
     * @param Request $request
     * @param integer $id
     *
     * @return void
     *
     * @throws \Doctrine\ORM\ORMException Default doctrine exception.
     */
    public function testUpdate(Request $request, $id)
    {
        $user = new Users();
        $this->repositoryMock->expects($this->once())
            ->method('find')
            ->with($id)
            ->will($this->returnValue($user));

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with($user)
            ->will($this->returnValue(null));

        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($user);

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $response = $this->usersDataStore->update($id, $request);

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * Data provider for test update.
     *
     * @return array
     */
    public function providerTestUpdate()
    {
        $request = new Request(['username' => 'test', 'email' => 'test@mail.com']);

        return [
            [
                $request,
                1
            ]
        ];
    }

    /**
     * Test update.
     *
     * @dataProvider providerTestCreate
     * @param Request $request
     * @param Users   $expectedUser
     *
     * @return void
     */
    public function testCreate(Request $request, $expectedUser)
    {
        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with($expectedUser)
            ->will($this->returnValue(null));

        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($expectedUser);

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $response = $this->usersDataStore->create($request);

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * Data provider for test update.
     *
     * @return array
     */
    public function providerTestCreate()
    {
        $request = new Request(['username' => 'test', 'email' => 'test@mail.com']);
        $user = new Users();
        $user->setUsername('test')
            ->setEmail('test@mail.com');

        return [
            [
                $request,
                $user
            ]
        ];
    }

    /**
     * Delete test.
     *
     * @return void
     *
     * @throws \Doctrine\ORM\ORMException Default doctrine exception.
     */
    public function testDelete()
    {
        $user = new Users();

        $this->repositoryMock->expects($this->once())
            ->method('find')
            ->with(1)
            ->will($this->returnValue($user));

        $this->entityManagerMock->expects($this->once())
            ->method('remove')
            ->with($user);

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $response = $this->usersDataStore->delete(1);
        $this->assertInstanceOf(Response::class, $response);
    }
}
