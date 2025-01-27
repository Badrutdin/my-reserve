<?php

namespace App\Validator\Context;

use App\Validator\ValidationContextInterface;

enum PositionValidationContext implements ValidationContextInterface
{
    case ROLE_CREATE_VALIDATION;
    case ROLE_UPDATE_VALIDATION;

    public function getContext(): array
    {
        return match ($this) {
            self::ROLE_CREATE_VALIDATION => [
                'fields' => [
                    'name' => [
                        'required' => true,
                        'constraints' => [
                            'NotBlank' => [],
                            'Length' => ['max' => 255],
                            'Type' => ['type' => 'string']
                        ]
                    ]
                ]
            ],
            self::ROLE_UPDATE_VALIDATION => [
                'fields' => [
                    'name' => [
                        'required' => false,
                        'constraints' => [
                            'NotBlank' => [],
                            'Length' => ['max' => 255],
                            'Type' => ['type' => 'string']
                        ],
                    ]

                ]
            ]
        };
    }
}
