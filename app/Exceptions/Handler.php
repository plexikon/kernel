<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
            $statusCode = $exception->getCode() === 0 ? 500 : $exception->getCode();

            return new JsonResponse([
                'data' => [
                    'message' => $exception->getMessage(),
                    'status_code' => $statusCode
                ]
            ], $statusCode);
        }

        return parent::render($request, $exception);
    }
}
