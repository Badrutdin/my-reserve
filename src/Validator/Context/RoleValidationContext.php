<?php

namespace App\Validator\Context;

use App\Security\Permission;
use App\Validator\ValidationContextInterface;

enum RoleValidationContext implements ValidationContextInterface
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
                    ],
                    'permissions' => [
                        'required' => true,
                        'constraints' => [
                            'NotBlank' => [], // Поле должно быть заполнено
                            'Type' => ['type' => 'array'], // Проверяем, что это массив
                            'All' => [ // Проверка каждого элемента массива
                                'constraints' => [
                                    'Choice' => [ // Обратите внимание на регистр "Choice"
                                        'choices' => array_map(fn($permission) => $permission->value, Permission::cases()), // Все допустимые значения
                                        'multiple' => false, // Каждый элемент массива проверяется отдельно
                                        'message' => 'Invalid permission "{{ value }}" provided.'
                                    ]
                                ]
                            ],
                            'Unique' => [] // Проверка на уникальность значений в массиве
                        ]
                    ]

                ]
            ]
        };
    }
}
