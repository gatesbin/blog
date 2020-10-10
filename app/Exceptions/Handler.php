<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use TechSoft\Laravel\Exception\ExceptionReportHandleTrait;

class Handler extends ExceptionHandler
{
    use ExceptionReportHandleTrait;

    
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
    ];

    
    public function report(Exception $exception)
    {
        $this->errorReportCheck($exception);
        parent::report($exception);
    }

    
    public function render($request, Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        }

        return parent::render($request, $e);
    }

    
    protected function convertExceptionToResponse(Exception $e)
    {
        if (env('APP_DEBUG', true)) {
            return parent::convertExceptionToResponse($e);
        }
        return response()->view('errors.500', ['exception' => $e], 500);
    }
}
