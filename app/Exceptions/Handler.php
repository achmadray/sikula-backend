<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
   protected function unauthenticated($request, AuthenticationException $exception)
{
    // Jika permintaan adalah API, kirim response JSON
    if ($request->expectsJson() || $request->is('api/*')) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    // Jangan redirect ke route 'login' karena tidak ada di backend API
    return response()->json(['message' => 'Unauthenticated. No login route available.'], 401);
}
}
