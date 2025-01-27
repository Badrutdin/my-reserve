<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntityValidator
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validate(array $data, ValidationContextInterface $context): array
    {
        $fields = $context->getContext()['fields'] ?? [];
        $constraints = $this->buildConstraints($fields);
        $violations = $this->validator->validate($data, new Assert\Collection($constraints));

        return $this->formatViolations($violations);
    }
private function test($constraints){
    $fieldConstraints = [];
    foreach ($constraints as $constraintName => $constraintOptions) {
        $className = "Symfony\\Component\\Validator\\Constraints\\$constraintName";

        if (class_exists($className)) {
            // Обрабатываем вложенные массивы через рекурсию
                    if ($constraintName === 'All' && isset($constraintOptions['constraints'])) {
                        $constraintOptions = $this->test($constraintOptions['constraints']);
                    }

            $fieldConstraints[] = new $className($constraintOptions);
        }
    }
    return $fieldConstraints;
}
    private function buildConstraints(array $fields): array
    {
        $constraints = [];

        foreach ($fields as $fieldName => $fieldConfig) {
            $fieldConstraints = $this->test($fieldConfig['constraints']);


            if (!empty($fieldConfig['required']) && $fieldConfig['required'] === true) {
                $constraints[$fieldName] = new Assert\Required($fieldConstraints);
            } else {
                $constraints[$fieldName] = new Assert\Optional($fieldConstraints);
            }
        }

        return $constraints;
    }

    private function buildSingleConstraint(array $constraintConfig): object
    {
        $constraintName = key($constraintConfig);
        $constraintOptions = current($constraintConfig);

        $className = "Symfony\\Component\\Validator\\Constraints\\$constraintName";

        if (!class_exists($className)) {
            throw new \InvalidArgumentException("Constraint class $className does not exist.");
        }

//        if ($constraintName === 'All' && isset($constraintOptions['constraints'])) {
//            $constraintOptions['constraints'] = array_map(
//                fn($nestedConstraint) => $this->buildSingleConstraint($nestedConstraint),
//                $constraintOptions['constraints']
//            );
//        }

        return new $className($constraintOptions);
    }

    private function formatViolations($violations): array
    {
        $errors = [];
        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $path = $violation->getPropertyPath();
                $message = $violation->getMessage();
                $invalidValue = $violation->getInvalidValue();

                $errors[$path] = [
                    'message' => $message,
                    'invalid_value' => $invalidValue,
                ];
            }
        }

        return $errors;
    }
}
