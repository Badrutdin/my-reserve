<?php

namespace App\Validator\Context;

use App\Validator\ValidationContextInterface;

enum UserValidationContext implements ValidationContextInterface
{
    case USER_CREATE_VALIDATION;
    case USER_UPDATE_VALIDATION;

    public function getContext(): array
    {
        return match ($this) {
            self::USER_CREATE_VALIDATION => [
                'fields' => [
                    'name' => [
                        'required' => true,
                        'constraints' => [
                            'NotBlank' => [],
                            'Length' => ['max' => 255],
                            'Type' => ['type' => 'string']
                        ],
                    ],
                    'role' => [
                        'required' => false,
                        'constraints' => [
                            'NotBlank' => [],
                            'Type' => ['type' => 'integer'],
                        ],
                    ],

                    'position' => [
                        'required' => false,
                        'constraints' => [
                            'NotBlank' => [],
                            'Type' => ['type' => 'integer'],
                        ],
                    ],
                    'email' => [
                        'required' => true,
                        'constraints' => [
                            'NotBlank' => [],
                            'Length' => ['max' => 255],
                            'Type' => ['type' => 'string'],
                            'Email' => [], // Проверка на корректность email
                        ],
                    ],
                    'password' => [
                        'required' => true,
                        'constraints' => [
                            'NotBlank' => [],
                            'Length' => ['min' => 8, 'max' => 64], // Минимальная и максимальная длина пароля
                            'Type' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
            self::USER_UPDATE_VALIDATION => [
                'fields' => [
                    'name' => [
                        'required' => false,
                        'constraints' => [
                            'NotBlank' => [],
                            'Length' => ['max' => 255],
                            'Type' => ['type' => 'string']
                        ],
                    ],
                    'role' => [
                        'required' => false,
                        'constraints' => [
                            'NotBlank' => [],
                            'Type' => ['type' => 'integer'],
                        ],
                    ],
                    'position' => [
                        'required' => false,
                        'constraints' => [
                            'NotBlank' => [],
                            'Type' => ['type' => 'integer'],
                        ],
                    ],
                ],
            ],
        };
    }
}
