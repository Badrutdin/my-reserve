<?php

namespace App\Service;

use App\DataTransformer\PaginationTransformer;
//use App\DataTransformer\EmployeeTransformer;
use App\Entity\Organization;
use App\Entity\Employee;
use App\Exception\ApiException;
use App\Manager\EmployeeManager;
use App\Repository\EmployeeRepository;
//use App\Validator\EmployeeValidationService;
use Doctrine\ORM\EntityManagerInterface;

class EmployeeService
{
    public function __construct(
        private EmployeeRepository        $employeeRepository,
//        private EmployeeTransformer       $employeeTransformer,
        private PaginationTransformer     $paginationTransformer,
        private EntityManagerInterface    $entityManager,
//        private EmployeeValidationService $employeeValidationService,
        private EmployeeManager           $employeeManager
    )
    {
    }

    public function getByOrganization(Organization $organization, int $page, int $limit): array
    {
        $employees = $this->employeeRepository->findByOrganization($organization);
        $total = $this->employeeRepository->listCount($organization);
        return [
            'pagination' => $this->paginationTransformer->transform($page, $limit, $total, ceil($total / $limit)),
//            'items' => array_map(fn($employee) => $this->employeeTransformer->transform($employee), $employees),
        ];
    }

    public function getById(Organization $organization, $employeeId): array
    {
        $employee = $this->employeeManager->findByOrganization($organization, $employeeId);
//        return [$this->employeeTransformer->transform($employee)];
    }

    public function create(Organization $organization, array $data): array
    {

//        $this->employeeValidationService->validateOnCreate($data);
        $employee = $this->employeeRepository->findOneBy(['name' => $data['name'], 'organization' => $organization]);
        if ($employee) ApiException::employeeAlreadyExistsException($employee->getId());
        $employee = new Employee();
        $this->employeeManager->setProperties($organization, $employee, $data);
        $this->save($employee);
//        return [$this->employeeTransformer->transform($employee)];
    }

    public function save(Employee $employee): void
    {
        if (!$this->entityManager->contains($employee)) $this->entityManager->persist($employee);
        $this->entityManager->flush();
    }

    public function update(Organization $organization, int $employeeId, array $data): array
    {
//        $this->employeeValidationService->validateOnUpdate($data);
        $employee = $this->employeeManager->findByOrganization($organization, $employeeId);
        $this->employeeManager->updateProperties($organization, $employee, $data);
        $this->save($employee);
//        return [$this->employeeTransformer->transform($employee)];
    }

    public function delete(Organization $organization, int $employeeId): array
    {
        $employee = $this->employeeManager->findByOrganization($organization, $employeeId);
        $associatedUsers = $this->employeeManager->findUsersByOrganizationAndId($employeeId, $organization->getId());
//        if ($associatedUsers) ApiException::employeeUsedUsersException($employeeId, $associatedUsers);
        $associatedEmployees = $this->employeeManager->findEmployees($employee);
//        if ($associatedEmployees) ApiException::employeeUsedEmployeesException($employeeId, $associatedEmployees);
        $this->entityManager->remove($employee);
        $this->entityManager->flush();
        return ['message' => "employee $employeeId is deleted"];
    }
}