<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
  /**
   * Display the login page.
   *
   * This method prepares the data to be passed to the login view, including setting the page title.
   * It then returns the view for the login page with the specified data.
   *
   * @return \Illuminate\Http\Response  Renders the 'Login' view with the page title.
   */
  public function login()
  {
    /* Prepare data to be passed to the view, including the page title */
    $data = ['title' => 'Login Page'];

    /* Return the 'Login' view with the prepared data */
    return view('Login', $data);
  }

  /**
   * Validate and authenticate user credentials.
   *
   * This method handles the incoming request to validate user credentials and attempt authentication.
   * It validates the email and password fields and checks if the user can be authenticated with the provided credentials.
   * It returns a JSON response indicating success or failure of the authentication attempt.
   *
   * @param \Illuminate\Http\Request $request The incoming request containing user credentials.
   * @return \Illuminate\Http\JsonResponse Returns a JSON response with the result of the authentication attempt.
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
          'error' => $validator->errors()->first()
        ]);
      }

      /* Attempt to authenticate the user with the provided email and password */
      if (Auth::attempt($request->only('email', 'password'))) {
        /* If authentication is successful, return a success response */
        return response()->json([
          'success' => true,
          'message' => 'User Authenticated Successfully.'
        ]);
      } else {
        /* If authentication fails, return an error response */
        return response()->json([
          'success' => false,
          'error' => 'Invalid Username or Password.'
        ]);
      }
    } catch (Exception $e) {
      /* Handle unexpected exceptions and return JSON response */
      return response()->json([
        'success' => false,
        'error' => 'An unexcepted error occured'
      ]);
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
