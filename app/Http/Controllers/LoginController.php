<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class LoginController extends Controller
{
  function login(){
    $data= [
        'title'=>'Login Page'
    ];
    
    return view('Login',$data);

  }
  public function checkCredentials(Request $request)
  {
      $this->validate($request, [
          'email' => 'required|email',
          'password' => 'required',
      ]);

      if (auth()->attempt($request->only('email', 'password'))) {
        $user = auth()->user();
        // $user_id = $user->id;

        return response()->json(['success' => true, 'message' => 'User Authenticated Successfully.']);
      } else {
          return response()->json(['success' => false, 'error' => 'Invalid Password.'], 401);
      }  
  }
  public function logoutUser()
  {
    Auth::logout();
    return redirect('/');   
  }

}
