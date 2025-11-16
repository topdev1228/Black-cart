<?php
declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\Exceptions\ApiExceptionErrorCodes;
use App\Enums\Exceptions\ApiExceptionTypes;
use Illuminate\Auth\AuthenticationException as LaravelAuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

        $this->renderable(function (ValidationException $exception, Request $request) {
            return response()->json([
                'type' => ApiExceptionTypes::REQUEST_ERROR,
                'code' => ApiExceptionErrorCodes::INVALID_PARAMETERS,
                'message' => $exception->getMessage(),
                'errors' => $exception->errors(),
            ], $exception->status);
        });

        $this->renderable(function (LaravelAuthenticationException $exception, Request $request) {
            return $this->shouldReturnJson($request, $exception)
                ? $this->render($request, new AuthenticationException($exception->getMessage()))
                : redirect()->guest($exception->redirectTo() ?? route('login'));
        });

        $this->renderable(function (NotFoundHttpException $exception, Request $request) {
            // $exception->getMessage() can be with ID or without ID like below, respectively:
            //  "No query results for model [App\Domain\Programs\Models\Program] non_existent_program_id" OR
            //  "No query results for model [App\Domain\Programs\Models\Program]."
            //  This regex will get the model name, in this example, "Program", and ignore the rest, including the ID as
            //  we may not want to expose internal IDs.

            preg_match(
                '/No query results for model \[.*\\\(.*)\]/',
                $exception->getMessage(),
                $matches
            );
            $model = $matches[1];

            $errorCode = ApiExceptionErrorCodes::tryFrom((string) Str::of($model)->lower()->append('_not_found'));
            if ($errorCode === null) {
                $errorCode = ApiExceptionErrorCodes::RESOURCE_NOT_FOUND;
            }

            return $this->render(
                $request,
                new NotFoundException(
                    sprintf('%s not found.', ucfirst($model)),
                    $errorCode,
                )
            );
        });
    }
}
