<?php

namespace App\Exception;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiException
{
    public static function userNotFoundException(int $id)
    {
        throw new Exception(json_encode(["message" => "User not found", "userId" => $id], 404));
    }

    public static function userAlreadyAssignException(int $id)
    {
        throw new Exception(json_encode(["message" => "User is already assigned to this organization", "userId" => $id]), 400);
    }

    public static function userNotAssignException(int $id)
    {
        throw new Exception("User is not assigned to this organization. id:$id", 400);
    }

    public static function positionNotFoundException(int $id)
    {
        throw new Exception("Position not found. id:$id", 404);
    }

    public static function organizationNotFoundException(string $subdomain)
    {
        throw new Exception("Organization not found. subdomain: $subdomain", 404);
    }

    public static function invalidOrganizationException(string $subdomain)
    {
        throw new Exception("invalid organization subdomain. subdomain: $subdomain", 400);
    }

    public static function emptyOrganizationException()
    {
        throw new Exception('Header "X-organization-id is empty"', 400);
    }

    public static function invalidJSONException()
    {
        throw new Exception("'Invalid JSON data", 400);
    }

    public static function validationException($errors)
    {
        throw new \Exception(json_encode($errors, 448), 400);
    }

    public static function roleNotFoundException(int $roleId)
    {
        throw new NotFoundHttpException(json_encode(["message" => "Role not found", "roleId" => $roleId], 404));
    }

    public static function roleAlreadyExistsException(int $roleId)
    {
        throw new Exception(json_encode(["message" => "role with this name already exists", "roleId" => $roleId]), 400);
    }
    public static function positionAlreadyExistsException(int $positionId)
    {
        throw new Exception(json_encode(["message" => "position with this name already exists", "roleId" => $positionId]), 400);
    }

    public static function roleCantEditExistsException(int $roleId)
    {
        throw new Exception(json_encode(["message" => "this role cannot be edited", "roleId" => $roleId]), 409);
    }

    public static function roleUsedException(int $roleId, array $associated_users)
    {
        throw new Exception(json_encode(["message" => "this role has associated users", "roleId" => $roleId, 'associated_usersIds' => $associated_users]), 400);
    }
    public static function positionUsedUsersException(int $positionId, array $associated_users)
    {
        throw new Exception(json_encode(["message" => "this position has associated users", "positionId" => $positionId, 'associated_usersIds' => $associated_users]), 400);
    }
    public static function positionUsedEmployeesException(int $positionId, array $associated_employees)
    {
        throw new Exception(json_encode(["message" => "this position has associated employees", "positionId" => $positionId, 'associated_employeesIds' => $associated_employees]), 400);
    }

    public static function employeeNotFoundException(int $employeeId)
    {
        throw new NotFoundHttpException(json_encode(["message" => "Employee not found", "employeeId" => $employeeId], 404));
    }
}
