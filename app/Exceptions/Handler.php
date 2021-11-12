<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Response;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
{
    if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
        return response()->json([
            'data' => [
                'message' => 'Resource not found',
                'status_code' => Response::HTTP_NOT_FOUND
            ]
        ], Response::HTTP_NOT_FOUND);
    } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
        return response()->json([
            'data' => [
                'message' => 'Endpoint not found',
                'status_code' => Response::HTTP_NOT_FOUND
            ]
        ], Response::HTTP_NOT_FOUND);
    }

    return response()->json([
            'data' => [
                'message' => $e->getMessage(),
                'status_code' => Response::HTTP_BAD_REQUEST
            ]
        ], Response::HTTP_BAD_REQUEST);
}

}
