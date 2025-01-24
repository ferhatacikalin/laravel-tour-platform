<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public function success(string $code, $data = [], int $status = 200): JsonResponse
    {
        $message = __('enum.'.$code.'.Message');
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $status);
    }

    public function fail(string $code, $data = [], int $status = 400): JsonResponse
    {
        $message = __('enum.'.$code.'.Message');
        $code = __('enum.'.$code.'.Code');
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data,
            'code' => $code
        ], $status);
    }
}
