<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\AssignedSeats;
use App\Models\Roles;
use App\Models\Teams;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function register()
    {
        /* Set the title for the registration page */
        $data = ['title' => 'Register Page'];

        /* Render the 'signup' view with the provided data */
        return view('signup', $data);
    }

    public function registerUser(Request $request)
    {
        /* Validate request data */
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[!@#$%^&*(),.?":{}|<>]).*$/',
                'confirmed'
            ],
            'company' => 'required',
            'termsCheckbox' => 'required'
        ], [
            'password.regex' => 'The password must include at least one special character.',
            'termsCheckbox.required' => 'Terms and conditions must be checked'
        ]);

        /* Return validation errors if validation fails */
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        /* Use a database transaction */
        DB::beginTransaction();

        try {
            /* Find the Owner role */
            $role = Roles::where('role_name', 'Owner')->first();

            /* Handle case where no role is found */
            if (empty($role)) {
                return redirect()->back()->withErrors(['error' => 'Something went wrong'])->withInput();
            }

            /* Create new team */
            $team = Teams::create([
                'team_name' => $request->input('company')
            ]);

            /* Create new user */
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'team_id' => $team->id
            ]);

            /* Create new Assigned Seats */
            AssignedSeats::create([
                'user_id' => $user->id,
                'role_id' => $role->id,
                'seat_id' => 0,
            ]);

            /* Commit the transaction */
            DB::commit();

            /* Sending a welcome email */
            Mail::to($user->email)->send(new WelcomeMail($user));

            /* Redirect back with success message */
            return redirect()->route('login')->with('success', 'User registered successfully');
        } catch (\Throwable $e) {
            /* Rollback the transaction if something fails */
            DB::rollBack();

            /* Log the exception message for debugging */
            Log::error($e);

            /* Redirect back with error message */
            return redirect()->back()->withErrors(['error' => 'Something went wrong'])->withInput();
        }
    }

    public function verifyAnEmail($email)
    {
        dd($email);
    }
}
