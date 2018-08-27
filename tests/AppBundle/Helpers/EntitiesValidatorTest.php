<?php

namespace Tests\AppBundle\Helpers;

use AppBundle\Entity\Users;
use AppBundle\Helpers\EntitiesValidator;
use phpDocumentor\Reflection\Types\Array_;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class EntitiesValidatorTest
 * @package Tests\AppBundle\Helpers
 */
class EntitiesValidatorTest extends TestCase
{
    /**
     * @var ValidatorInterface
     */
    private $validatorMock;

    /**
     * @var ConstraintViolation
     */
    private $violationMock;

    /**
     * @var EntitiesValidator
     */
    private $validator;

    /**
     * Setup method.
     */
    public function setUp()
    {
        $this->validatorMock = $this->getMockBuilder(ValidatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->violationMock = $this->getMockBuilder(ConstraintViolation::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->validator = new EntitiesValidator($this->validatorMock);
    }

    /**
     * Test validate.
     *
     * @dataProvider providerTestValidate
     * @param object $entity Entity.
     *
     * @return void
     */
    public function testValidate($entity)
    {
        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with($entity)
            ->will($this->returnValue([$this->violationMock]));

        $this->violationMock->expects($this->once())
            ->method('getMessage')
            ->will($this->returnValue('error'));

        $this->validator->validate($entity);
    }

    /**
     * Data provider for test validate.
     *
     * @return array
     */
    public function providerTestValidate()
    {
        $user = new Users();
        $user->setUsername('hello');
        $user->setEmail('test');

        return [
            [
                $user
            ]
        ];
    }
}
