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
    public function testTest()
    {
        $this->assertTrue(true);
    }
}
