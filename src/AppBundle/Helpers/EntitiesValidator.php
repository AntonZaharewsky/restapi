<?php

namespace AppBundle\Helpers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class EntitiesValidator
 * @package AppBundle\Helpers
 */
class EntitiesValidator
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * EntitiesValidator constructor.
     * @param ValidatorInterface $validator Validator.
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Validate entity.
     *
     * @param object $entity Entity.
     * @return array
     */
    public function validate($entity)
    {
        $errors = [];
        $violations = $this->validator->validate($entity);

        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
        }

        return $errors;
    }
}
