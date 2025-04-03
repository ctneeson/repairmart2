<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Throwable;

class Handler extends ExceptionHandler
{
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

        // Add this code to handle post too large exception
        $this->renderable(function (PostTooLargeException $e, $request) {
            if ($request->is('listings*')) {
                return redirect()->back()->withErrors([
                    'attachments' => 'The uploaded files are too large. Please reduce the file size or upload fewer files. Maximum total size is ' . ini_get('post_max_size') . '.'
                ])->withInput();
            }
        });
    }
}