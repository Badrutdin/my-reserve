<?php

namespace App\DataTransformer;

use App\DTO\PositionDTO;
use App\Entity\Position;

class PositionTransformer
{
    public function transform(Position $user): PositionDTO
    {
        return new PositionDTO(
            id: $user->getId(),
            name: $user->getName()
        );
    }
}