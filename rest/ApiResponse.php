<?php

namespace Rest;

use GuzzleHttp\Psr7\Response;
use JsonException;

final class ApiResponse
{
    private const JSON_FLAGS = JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION;
    private const DEFAULT_HEADERS = [
        'Content-Type' => 'application/json',
        'Cache-Control' => 'no-cache, no-store, must-revalidate'
    ];

    public static function success(
        mixed $data,
        int $statusCode = 200,
        array $headers = []
    ): Response {
        $headers = array_merge(self::DEFAULT_HEADERS, $headers);

        try {
            return new Response(
                $statusCode,
                $headers,
                json_encode([
                    'status' => 'success',
                    'data' => $data,
                    'timestamp' => time()
                ], self::JSON_FLAGS)
            );
        } catch (JsonException $e) {
            return self::error(500, 'JSON encoding error');
        }
    }

    public static function error(
        int $code,
        string $message,
        ?array $details = null,
        array $headers = []
    ): Response {
        $headers = array_merge(self::DEFAULT_HEADERS, $headers);

        $error = [
            'status' => 'error',
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
            'timestamp' => time()
        ];

        if ($details !== null) {
            $error['error']['details'] = $details;
        }

        try {
            return new Response(
                $code,
                $headers,
                json_encode($error, self::JSON_FLAGS)
            );
        } catch (JsonException $e) {
            return new Response(
                500,
                ['Content-Type' => 'text/plain'],
                'Internal Server Error: Failed to encode error response'
            );
        }
    }

    public static function emptyResponse(
        int $statusCode = 204,
        array $headers = []
    ): Response {
        return new Response($statusCode, $headers);
    }

    public static function optionsResponse(
        array $allowedMethods,
        array $headers = []
    ): Response {
        $headers = array_merge([
            'Access-Control-Allow-Methods' => implode(', ', $allowedMethods),
            'Access-Control-Allow-Headers' => 'Content-Type'
        ], $headers);

        return self::emptyResponse(204, $headers);
    }
}
