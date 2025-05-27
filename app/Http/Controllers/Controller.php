<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
        /**
     * Returns as success with json response
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function success(mixed $data, string $message = "okay", int $statusCode = 200)
    {
        return response()->json([
            'data' => $data,
            'success' => true,
            'message' => $message,
        ], $statusCode);
    }

    /**
     * Returns as error with json response
     *
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function error(string $message, int $statusCode = 400) 
    {
        return response()->json([
            'data' => null,
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }
}
