<?php

namespace App\DataTransformer;

use App\DTO\RoleDTO;
use App\Entity\Role;

class RoleTransformer
{
    public function transform(Role $user): RoleDTO
    {
        return new RoleDTO(
            id: $user->getId(),
            name: $user->getName(),
            permissions: $user->getPermissions()
        );
    }
}