<?php

namespace App\DTO;

class RoleDTO
{
    public function __construct(
        public int     $id,
        public string  $name,
        public array  $permissions,
    ) {}
}