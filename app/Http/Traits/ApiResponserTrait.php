<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Log;

trait ApiResponserTrait
{

    protected function successResponse($message = null, $data = null, $code = 200)
    {
        return response()->json([
            'status' => 'Success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function createdResponse($message = null, $data = null)
    {
        return response()->json([
            'status' => 'Success',
            'message' => $message,
            'data' => $data
        ], 201);
    }

    protected function errorResponse($message = null, $code = 400)
    {
        response()->json([
            'status' => 'Error',
            'message' => $message
        ], $code)->throwResponse();
    }

    protected function notFoundResponse($message = null)
    {
        response()->json([
            'status' => 'Not found',
            'message' => $message,
        ], 404)->throwResponse();
    }

    protected function forbiddenResponse($message = null)
    {
        response()->json([
            'status' => 'Forbidden',
            'message' => $message
        ], 403)->throwResponse();
    }

    protected function unprocessableContentResponse($message = null)
    {
        return response()->json([
            'status' => 'Unprocessable Content',
            'message' => $message,
        ], 422);
    }

    protected function serverErrorResponse($message = null, $code = 500)
    {
        Log::channel('server-error')->error($message);
        response()->json([
            'status' => 'Server Error',
            'message' => $message
        ], $code)->throwResponse();
    }


}

?>
