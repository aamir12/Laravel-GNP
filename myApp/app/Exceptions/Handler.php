<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'cpassword',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (AuthenticationException $e) {
            return response()->error(__('handler')['auth_exception'], null, 401);
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->error(__('handler')['404'], null, 404);
            }
        });

        $this->renderable(function (TokenMismatchException $e) {
            return response()->view('errors.tokenMisMatch', ['exception' => $e]);
        });

        // if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
        //     return response()->view('errors.tokenMisMatch', ['exception' => $exception]);
        // }

        // $this->renderable(function (ValidationException $e) {
        //     return response()->error(null, $e->errors());
        // });
    }
}
