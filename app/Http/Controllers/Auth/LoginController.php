<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

use Auth;


class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';  // This is correct
    }

    protected function validateLogin(Request $request)
    {
        dd($request->all());
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }
}