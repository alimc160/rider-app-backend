<?php

namespace App\Exceptions;

use App\Http\Traits\ApiResponserTrait;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Support\ItemNotFoundException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponserTrait;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
        $this->renderable(function (\Spatie\Permission\Exceptions\UnauthorizedException $exception, $request) {
            $this->errorResponse('You have no rights to access this url', 403);
        });
        $this->renderable(function (ItemNotFoundException $exception, $request) {
            if ($request->wantsJson()) {
                $message = $exception->getMessage();
                if (empty($exception->getMessage())) {
                    $message = "Record not exist against given credentials.";
                }
                $this->serverErrorResponse($message);
            }
        });
        $this->renderable(function (PostTooLargeException $exception,$request){
           if ($request->wantsJson()){
               $message = $exception->getMessage();
               if (empty($exception->getMessage())) {
                   $message = "File too large.";
               }
               $this->serverErrorResponse($message);
           }
        });
        $this->renderable(function (MassAssignmentException $exception,$request){
           if ($request->wantsJson()){
               $message = $exception->getMessage();
               if (empty($exception->getMessage())) {
                   $message = "Something went wrong.";
               }
               $this->serverErrorResponse($message);
           }
        });
        $this->renderable(function (RelationNotFoundException $exception,$request){
            if ($request->wantsJson()){
                $message = $exception->getMessage();
                if (empty($exception->getMessage())) {
                    $message = "Something went wrong.";
                }
                $this->serverErrorResponse($message);
            }
        });
        $this->renderable(function (MethodNotAllowedHttpException $exception,$request){
            if ($request->wantsJson()){
                $message = $exception->getMessage();
                if (empty($exception->getMessage())) {
                    $message = "Something went wrong.";
                }
                $this->serverErrorResponse($message);
           }
        });
        $this->renderable(function (NotFoundHttpException $exception,$request){
            if ($request->wantsJson()){
                $message = $exception->getMessage();
                if (empty($exception->getMessage())) {
                    $message = "URL not found";
                }
                $this->errorResponse($message,404);
            }
        });
    }
}
