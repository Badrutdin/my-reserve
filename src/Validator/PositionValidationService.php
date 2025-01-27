<?php

namespace App\Validator;

use App\Exception\ApiException;
use App\Validator\Context\PositionValidationContext;

class PositionValidationService
{
    public function __construct(private EntityValidator $positionValidator)
    {
    }

    public function validateOnCreate(array $data): void
    {

        $errors = $this->positionValidator->validate($data, PositionValidationContext::ROLE_CREATE_VALIDATION);
        if (!empty($errors)) ApiException::validationException($errors);
    }

    public function validateOnUpdate(array $data): void
    {
        $errors = $this->positionValidator->validate($data, PositionValidationContext::ROLE_UPDATE_VALIDATION);
        if (!empty($errors)) ApiException::validationException($errors);
    }
}
