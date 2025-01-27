<?php

namespace App\Validator;

use App\Exception\ApiException;
use App\Validator\Context\RoleValidationContext;

class RoleValidationService
{
    public function __construct(private EntityValidator $roleValidator)
    {
    }

    public function validateOnCreate(array $data): void
    {

        $errors = $this->roleValidator->validate($data, RoleValidationContext::ROLE_CREATE_VALIDATION);
        if (!empty($errors)) ApiException::validationException($errors);
    }

    public function validateOnUpdate(array $data): void
    {
        $errors = $this->roleValidator->validate($data, RoleValidationContext::ROLE_UPDATE_VALIDATION);
        if (!empty($errors)) ApiException::validationException($errors);
    }
}
