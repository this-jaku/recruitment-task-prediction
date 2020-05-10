<?php

namespace App\Service\Core;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RestRequestValidator
{
    /** @var ValidatorInterface */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param string $requestContent
     * @param Collection $requestValidationRules
     * @return array of violations, each contain message, path, value
     */
    public function validateJsonContent(string $requestContent, Collection $requestValidationRules = null): array
    {
        $violations = [];
        $decodedContent = json_decode($requestContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $violations[] = [
                'message' => 'failed to decode content',
                'path' => '/',
                'value' => $requestContent
            ];
        } elseif ($requestValidationRules) {
            $violations = $this->validate($decodedContent, $requestValidationRules);
        }

        return $violations;
    }

    private function validate(array $body, Collection $requestValidation): array
    {
        $violations = $this->validator->validate($body, $requestValidation);

        $simplifiedViolations = [];
        if (count($violations) > 0) {
            /** @var ConstraintViolationInterface $violation */
            foreach ($violations as $violation) {
                $simplifiedViolations[] = [
                    'message' => $violation->getMessage(),
                    'path' => $violation->getPropertyPath(),
                    'value' => $violation->getInvalidValue()
                ];
            }
        }

        return $simplifiedViolations;
    }
}