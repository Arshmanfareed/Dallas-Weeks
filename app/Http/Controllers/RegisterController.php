<?php

namespace App\Http\Controllers;

use App\Models\User;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    function register()
    {
        $date = [
            'title' => 'Register Page'
        ];
        return view('signup', $date);
    }

    function registerUser(Request $request)
    {
        // dd($request);
        // echo $request->input('name');
        // echo $request->input('email');
        // echo $request->input('password');
        // echo $request->input('confirm_password');

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            // If validation fails, return to the signup page with errors
            return back()->withErrors($validator)->withInput();
        }


        // Hash the password before storing it in the database
        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ];

        // Create a new user record
        $user = User::create($data);

        return back()->with(['success' => 'User registered successfully', 'data' => $user]);
    }
}
