<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

use Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Redirection after login.
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
        $this->middleware('guest')->except('logout');
    }

    /**
     * Override the method to use 'username' instead of 'email'.
     *
     * @return string
     */
    public function username()
    {
        return 'username';  // Set to 'username' instead of default 'email'
    }

    /**
     * Validate the login request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateLogin(Request $request)
    {
        return $request->validate([
            $this->username() => 'required|string', // Validate username
            'password' => 'required|string',
        ]);
    }

    /**
     * Attempt to log the user in.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }
}
