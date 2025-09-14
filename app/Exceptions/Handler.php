<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
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

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Handle HTTP exceptions with custom error pages
        if ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();

            // Check if we have a custom error view for this status code
            $customViews = [403, 404, 419, 429, 500, 503];

            if (in_array($statusCode, $customViews)) {
                if (view()->exists("errors.{$statusCode}")) {
                    return response()->view("errors.{$statusCode}", [
                        'exception' => $e,
                    ], $statusCode);
                }
            }
        }

        // Handle 500 errors (non-HTTP exceptions)
        if (!$this->isHttpException($e) && !config('app.debug')) {
            if (view()->exists('errors.500')) {
                return response()->view('errors.500', [
                    'exception' => $e,
                ], 500);
            }
        }

        return parent::render($request, $e);
    }

    /**
     * Convert an authentication exception into a response.
     */
    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $exception->getMessage()], 401);
        }

        return redirect()->guest($exception->redirectTo() ?? route('login'));
    }

    /**
     * Prepare a JSON response for the given exception.
     */
    protected function prepareJsonResponse($request, Throwable $e)
    {
        return new Response(
            $this->convertExceptionToArray($e),
            $this->isHttpException($e) ? $e->getStatusCode() : 500,
            $this->isHttpException($e) ? $e->getHeaders() : [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Convert the given exception to an array.
     */
    protected function convertExceptionToArray(Throwable $e)
    {
        return config('app.debug') ? [
            'message' => $e->getMessage(),
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => collect($e->getTrace())->map(fn ($trace) => Arr::except($trace, ['args']))->all(),
        ] : [
            'message' => $this->isHttpException($e) ? $e->getMessage() : 'Server Error',
        ];
    }
}