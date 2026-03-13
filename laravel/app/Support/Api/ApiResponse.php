<?php

namespace App\Support\Api;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function ok(array $payload = [], int $status = 200): JsonResponse
    {
        return response()->json($payload, $status);
    }

    public static function created(array $payload = []): JsonResponse
    {
        return response()->json($payload, 201);
    }

    public static function message(string $message, array $extra = [], int $status = 200): JsonResponse
    {
        return response()->json(array_merge(['message' => $message], $extra), $status);
    }

    public static function error(string $message, int $status = 400): JsonResponse
    {
        return response()->json(['error' => $message], $status);
    }

    public static function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return self::error($message, 403);
    }

    public static function notFound(string $message = 'Not found'): JsonResponse
    {
        return self::error($message, 404);
    }

    public static function paginated(array $results, int $total, int $page, int $pageSize): JsonResponse
    {
        return response()->json([
            'results' => $results,
            'total' => $total,
            'page' => $page,
            'page_size' => $pageSize,
        ]);
    }
}