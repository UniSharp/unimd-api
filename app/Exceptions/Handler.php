<?php

namespace App\Exceptions;

use Exception;
use BadMethodCallException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Illuminate\Http\JsonResponse;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        // Default response of 400
        $isFatal = $e instanceof FatalThrowableError;
        $isBadMethod = $e instanceof BadMethodCallException;
        $status = 400;

        if ($isFatal || $isBadMethod) {
            $status = 500;
        } else if (method_exists($e, 'getStatusCode')) {
            $status = $e->getStatusCode();
        }
        $message = $e->getMessage();

        if ($e instanceof ValidationException) {
            $status = $e->response->getStatusCode();
            $message = json_decode($e->response->getContent());
        }

        $response = [
            'error' => [
                'type' => get_class($e),
                'code' => $status,
                'message' => $message
            ],
        ];
        // Add debug trace
        if (env('APP_DEBUG')) {
            $response['error']['trace'] = $e->getTrace();
        }
        // Return a JSON response with the response array and status code
        return response()->json($response, $status);
    }
}
