<?php

namespace OCA\CrmConnector\Response;

use OCP\AppFramework\Http\JSONResponse;

trait CrmConnectionResponse
{
    public function __construct()
    {

    }

    public function success($data): JSONResponse
    {
        $success = [
            'success' => true,
            'data' => $data
        ];
        return new JSONResponse(
            $success, 200
        );
    }

    public function fail($exception): JSONResponse
    {
        $fail = [
            'success' => false,
            'error' => [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage()
            ]
        ];
        return new JSONResponse(
            $fail, $exception->getCode()
        );
    }
}