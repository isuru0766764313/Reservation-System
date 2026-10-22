<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
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

        

        // added to eleminate 419|page expired
        $this->renderable(function (TokenMismatchException $e, $request) {
            // Clear all session data to prevent cross-session conflicts
            $request->session()->flush();
            $request->session()->regenerate();
            
            return redirect()->route('home_route')
                ->with('error_key_2', 'Your session has expired. Please login again.');
        });

        // Uploaded file exceeded the server's post_max_size limit.
        // Instead of showing the raw PostTooLargeException error page,
        // flash a validation-style error and send the user back to the form.
        $this->renderable(function (PostTooLargeException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The uploaded file is too large. Maximum allowed size is 10MB.',
                ], Response::HTTP_REQUEST_ENTITY_TOO_LARGE);
            }

            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'clearence_form' => 'The uploaded file is too large. Maximum allowed size is 10MB. Please choose a smaller PDF file.',
                ]);
        });
    }
    
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Handle 419 Page Expired errors (CSRF token mismatch or session expired)
        if ($this->isHttpException($exception) && $exception->getStatusCode() == 419) {
            // Clear session to prevent cross-session conflicts
            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            
            // If it's an AJAX request, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your session has expired. Please refresh the page and try again.'
                ], 419);
            }
            
            // For regular requests, redirect to home
            return redirect()->route('home_route')
                ->with('error_key_2', 'Your session has expired. Please login again.');
        }

        return parent::render($request, $exception);
    }
}
