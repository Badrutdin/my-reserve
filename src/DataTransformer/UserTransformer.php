<?php

namespace App\DataTransformer;

use App\DTO\UserDTO;
use App\Entity\User;

class UserTransformer
{
    public function transform(User $user, int $organizationId): UserDTO
    {
        return new UserDTO(
            id: $user->getId(),
            name: $user->getName(),
            role: $user->getRoleByOrganization($organizationId)?->getId(),
            position: $user->getPositionByOrganization($organizationId)?->getId(),
        );
    }
}
