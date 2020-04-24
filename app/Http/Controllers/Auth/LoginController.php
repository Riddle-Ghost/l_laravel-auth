<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\models\User;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(LoginRequest $request)
    {
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function authenticated(LoginRequest $request, $user)
    {
        if ($this->isValidUser($user) ) {

            return redirect()->intended($this->redirectPath());
        }
        
        Auth::logout();
        return back();
    }

    private function isValidUser($user)
    {
        if (!$user->hasVerifiedEmail()) {
            
            session()->flash('error', 'Требуется подтвердить аккаунт. Пожалуйста проверьте Ваш эмейл');
        }
        elseif ($user->isBanned()) {
            
            session()->flash('error', 'Вы забанены');
        }
        return $user->hasVerifiedEmail() && !$user->isBanned();
    }
}