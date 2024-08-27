<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class LoginController extends Controller
{
  function login()
  {
    $data = [
      'title' => 'Login Page'
    ];

    return view('Login', $data);
  }

  public function checkCredentials(Request $request)
  {
    $this->validate($request, [
      'email' => 'required|email',
      'password' => 'required',
    ]);

    if (Auth::attempt($request->only('email', 'password'))) {
      $user = Auth::user();
      return response()->json(['success' => true, 'message' => 'User Authenticated Successfully.']);
    } else {
      return response()->json(['success' => false, 'error' => 'Invalid Username or Password.']);
    }
  }

  public function logoutUser()
  {
    Auth::logout();
    return redirect('/');
  }
}
