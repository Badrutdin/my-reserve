<?php

namespace App\Manager;

use App\Entity\Organization;
use App\Entity\Employee;
use App\Exception\ApiException;
use App\Repository\EmployeeRepository;
use App\Repository\UserRepository;


class EmployeeManager
{
    public function __construct(
        private EmployeeRepository $employeeRepository,
        private UserRepository     $userRepository
    )
    {
    }


    public function findByOrganization(Organization $organization, int $employeeId): Employee
    {
        $employee = $this->employeeRepository->findByOrganizationAndId($organization, $employeeId);
        if (!$employee) ApiException::employeeNotFoundException($employeeId);
        return $employee;
    }


    public function findUsersByOrganizationAndId($employeeId, $organizationId): array
    {
        return $this->userRepository->findByEmployeeAndOrganization($employeeId, $organizationId);
    }

    public function findEmployees(Employee $employee): array
    {
        return $employee->getEmployees()->map(fn($employee) => $employee->getId())->toArray();
    }


    public function setProperties(Organization $organization, Employee $employee, array $data): void
    {
        $employee->setName($data['name']);
        $employee->setOrganization($organization);
    }

    public function updateProperties(Organization $organization, Employee $employee, array $data): void
    {
        if (isset($data['name'])) $employee->setName($data['name']);
    }

}