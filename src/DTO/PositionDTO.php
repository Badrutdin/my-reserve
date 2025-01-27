<?php

namespace App\DTO;

class PositionDTO
{
    public function __construct(
        public int     $id,
        public string  $name
    ) {}
}