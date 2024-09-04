<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
  public function login()
  {
    /* Prepare data to be passed to the view, including the page title */
    $data = ['title' => 'Login Page'];

    /* Return the 'Login' view with the prepared data */
    return view('Login', $data);
  }

  public function checkCredentials(Request $request)
  {
    /* Validate the incoming request data */
    $validator = Validator::make($request->all(), [
      'email' => 'required|email',
      'password' => 'required',
    ]);

    /* Check if validation fails and return a JSON response with the first error message */
    if ($validator->fails()) {
      return response()->json(['success' => false, 'error' => $validator->errors()->first()]);
    }

    /* Attempt to authenticate the user with the provided email and password */
    if (Auth::attempt($request->only('email', 'password'))) {
      /* If authentication is successful, return a success response */
      return response()->json(['success' => true, 'message' => 'User Authenticated Successfully.']);
    } else {
      /* If authentication fails, return an error response */
      return response()->json(['success' => false, 'error' => 'Invalid Username or Password.']);
    }
  }

  public function logoutUser()
  {
    /* Log out the currently authenticated user */
    Auth::logout();

    /* Redirect the user to the homepage after logging out */
    return redirect('/');
  }
}
