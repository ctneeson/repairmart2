<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailVerifyController extends Controller
{
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect(route('home'))
            ->with('success', 'Email verified successfully. Account confirmation complete.');
    }

    /**
     * Show the email verification notice.
     *
     * @return \Illuminate\View\View
     */
    public function notice()
    {
        return view('auth.verify-email');
    }

    public function send(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect(route('home'))
                ->with('success', 'Email already verified.');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('success', 'Verification email sent successfully.');
    }

}
