<?php

namespace App\Security;

use App\Entity\Organization;
use App\Repository\UserRepository;

class Auth
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public  function checkUser($userId,Organization $organization): bool
    {
        $user = $this->userRepository->find($userId);
        return $user->getOrganizations()->contains($organization);
    }
}