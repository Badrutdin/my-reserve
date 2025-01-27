<?php

namespace App\Validator;

use App\Exception\ApiException;
use App\Validator\Context\UserValidationContext;

class UserValidationService
{
    public function __construct(private EntityValidator $userValidator)
    {
    }

    public function validateOnCreate(array $data): void
    {
        $errors = $this->userValidator->validate($data, UserValidationContext::USER_CREATE_VALIDATION);
        if (!empty($errors)) ApiException::validationException($errors);
    }

    public function validateOnUpdate(array $data): void
    {
        $errors = $this->userValidator->validate($data, UserValidationContext::USER_UPDATE_VALIDATION);
        if (!empty($errors)) ApiException::validationException($errors);
    }
}
