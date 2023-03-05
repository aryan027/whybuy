<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
    // public function register(): void
    // {
    //     $this->reportable(function (Throwable $e) {
    //         //
    //     });
    // }

    public function render($request, Throwable $exception)
    {
        if ($this->isHttpException($exception)) {
            $isAjax = 0;
            if($request->ajax()){
                $isAjax = 1;
            }
            if($exception->getStatusCode() == 404) {
                $logName = '404';
            }else{
                $logName = '500';
            }
            if($isAjax == 1){
                return response()->json(['status' => 'false'],$logName);
            }else{
                $segment = $request->segment(1);
                if($segment == 'admin'){
                    return response()->view('admin.errors.'.$logName);
                }
                return response()->view('errors.'.$logName);
            }
        }
        return parent::render($request, $exception);
    }
}
