<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Verified;

class VerificationController extends Controller
{

    use RedirectsUsers;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Show the email verification notice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {

        return view('auth.verify');
    }
 
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verify(Request $request)
    {
        if (!$user = User::where('verify_token', $request->token)->first()) {
            return redirect()->route('login')
                ->with('error', 'Ошибка. Неверная ссылка');
        }

        $user->email_verified_at = now();
        $user->verify_token = null;
        $user->save();

        event(new Verified($user));

        return redirect()->route('login')
            ->with('success', 'Эмейл успешно подтвержден. Теперь вы можете войти на сайт');
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        $user = User::where('email', $request['email'])->first();

        if($user) {

            $user->sendEmailVerificationNotification();
            return back()->with('resent', true);
        }
        else {

            return back()->with('error', 'Неверный эмейл');
        }
    }
}
