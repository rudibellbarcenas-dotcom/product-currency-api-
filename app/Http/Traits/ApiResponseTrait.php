<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Respuesta JSON de éxito estandarizada.
     *
     * @param  mixed  $data
     * @param  string|null  $message
     * @param  int  $statusCode
     * @return JsonResponse
     */
    protected function successResponse(mixed $data = null, ?string $message = null, int $statusCode = 200): JsonResponse
    {
        $payload = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        return response()->json($payload, $statusCode);
    }

    /**
     * Respuesta JSON de error estandarizada.
     *
     * @param  string  $message
     * @param  int  $statusCode
     * @param  mixed  $errors
     * @return JsonResponse
     */
    protected function errorResponse(string $message, int $statusCode = 400, mixed $errors = null): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ];

        return response()->json($payload, $statusCode);
    }
}
