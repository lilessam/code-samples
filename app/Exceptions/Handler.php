<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        /**
         * Because the v-uploader plugin doesn't use Axios or jQuery to upload the file
         * We're checking if there's a request for file upload endpoint and there was
         * an authentication exception being reported back. This way we can broadcast
         * an error to the frontend to refresh the page of the user with the request
         * token, so when the page is refreshed, other parts of the application
         * will log the user out.
         */
        if ($exception instanceof \Illuminate\Auth\AuthenticationException && str_contains(request()->url(), '/api/dashboard/files')) {
            broadcast(new \App\Events\SessionEnded(request()->cookie('token')));
        }
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }
}
