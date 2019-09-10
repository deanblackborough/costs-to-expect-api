<?php

namespace App\Exceptions;

use App\Events\InternalError;
use App\Models\ErrorLog;
use App\Utilities\Response;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\App;

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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof AuthenticationException) {
            if (App::environment() === 'local') {
                return response()->json(
                    [
                        'message' => 'Unauthenticated',
                        'trace' => $exception->getTraceAsString()
                    ],
                    403
                );
            } else {
                return response()->json(
                    [
                        'message' => trans('responses.authentication-required')
                    ],
                    403
                );
            }
        }

        $status_code = 500;
        if (method_exists($exception, 'getStatusCode') === true) {
            $status_code = $exception->getStatusCode();
        }

        $message = $exception->getMessage();

        switch ($status_code) {
            case 404:
                Response::notFound();
                break;
            case 503:
                response()->json(
                    [
                        'message' => trans('responses.maintenance')
                    ],
                    503
                )->send();
                exit;
                break;
            case 500:
                if (App::environment() === 'local') {
                    $response = [
                        'message' => $exception->getMessage(),
                        'trace' => $exception->getTraceAsString()
                    ];
                } else {
                    try {
                        $error_data = [
                            'message' => $exception->getMessage(),
                            'file' => $exception->getFile(),
                            'line' => $exception->getLine(),
                            'trace' => $exception->getTraceAsString()
                        ];

                        $error = new ErrorLog($error_data);
                        $error->save();

                        event(new InternalError($error_data));
                    } catch (Exception $e) {
                        // Don't worry for now, we just want to try and log some errors
                    }

                    $response = [
                        'message' => trans('responses.error')
                    ];
                }

                response()->json(
                    $response,
                    500
                )->send();
                exit;
                break;
            default:
                $message = $exception->getMessage();
                break;
        }

        return response()->json(
            [
                'message' => $message,
                'trace' => $exception->getTraceAsString()
            ],
            $status_code
        );
    }
}
