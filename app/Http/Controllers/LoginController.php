<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
  /**
   * Show the login page.
   *
   * @return \Illuminate\View\View
   */
  public function login()
  {
    /* Prepare data to be passed to the view, including the page title */
    $data = ['title' => 'Login Page'];

    /* Return the 'Login' view with the prepared data */
    return view('Login', $data);
  }

  /**
   * Handle the user authentication process.
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function checkCredentials(Request $request)
  {
    try {
      /* Validate the incoming request data */
      $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
      ]);

      /* Check if validation fails and return a JSON response with the first error message */
      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'error' => $validator->errors()->first(),
        ]);
      }

      /* Attempt to authenticate the user with the provided email and password */
      if (Auth::attempt($request->only('email', 'password'))) {
        /* If authentication is successful, return a success response */
        return response()->json([
          'success' => true,
          'message' => 'User Authenticated Successfully.',
          'dashboardUrl' => route('dashobardz'),
        ]);
      } else {
        /* If authentication fails, return an error response */
        return response()->json([
          'success' => false,
          'error' => 'Invalid Username or Password.',
        ]);
      }
    } catch (Exception $e) {
      /* Handle unexpected exceptions and return JSON response */
      return response()->json([
        'success' => false,
        'error' => 'An unexcepted error occured',
      ]);
    }
  }

  /**
   * Log out the currently authenticated user.
   *
   * @return \Illuminate\Http\RedirectResponse
   */
  public function logoutUser()
  {
    /* Log out the currently authenticated user */
    Auth::logout();

    /* Invalidate the session and regenerate the session ID */
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    /* Redirect the user to the homepage after logging out */
    return redirect('/');
  }
}
