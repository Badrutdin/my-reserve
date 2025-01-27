<?php

namespace App\Util;

use Symfony\Component\HttpFoundation\JsonResponse;

class JsonResponseHelper {
    public static function success(array $data,int $code = 200): JsonResponse {
        return new JsonResponse(['result' => $data], $code);
    }
    public static function error(string|array $message, int $code): JsonResponse {
        return new JsonResponse(['error' => $message], $code);
    }
}