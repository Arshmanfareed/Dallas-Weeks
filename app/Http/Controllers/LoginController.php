<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
  public function login()
  {
    $data = ['title' => 'Login Page'];
    return view('Login', $data);
  }

  public function checkCredentials(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required|email',
      'password' => 'required',
    ]);
    if ($validator->fails()) {
      return response()->json(['success' => false, 'error' => $validator->errors()->first()]);
    }
    if (Auth::attempt($request->only('email', 'password'))) {
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
