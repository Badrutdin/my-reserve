<?php

namespace App\Util;

use App\Entity\Organization;
use App\Entity\User;
use App\Exception\ApiException;
use App\Repository\OrganizationRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service_locator;

class RequestHelper
{
private static UserRepository $userRepository;
    public static function getPageParam(Request $request, string $key, int $default, int $min = 1, int $max = PHP_INT_MAX): int
    {
        $value = (int)$request->query->get($key, $default);
        return max($min, min($value, $max));
    }

    public static function getOrganization(Request $request, OrganizationRepository $organization_repository): ?Organization
    {
        $host = $request->getHost();
        $subdomain = explode('.', $host)[0];
        try {
            $organization = $organization_repository->findOneBy(['subdomain' => $subdomain]);

        } catch (\Exception $e) {
            ApiException::invalidOrganizationException($subdomain);
        }
        if (!$organization) {
            ApiException::organizationNotFoundException($subdomain);
        }
        return $organization;
    }

    public static function decodeRequestData($data): array
    {
        $result = json_decode($data, true);

        if (json_last_error() !== JSON_ERROR_NONE || gettype($result) != 'array') ApiException::invalidJSONException();
        return $result;
    }


}
