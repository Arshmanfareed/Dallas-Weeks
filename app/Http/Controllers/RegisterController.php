<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\AssignedSeats;
use App\Models\GlobalPermission;
use App\Models\Roles;
use App\Models\Teams;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /**
     * Show the registration page.
     *
     * @return \Illuminate\View\View
     */
    public function register()
    {
        /* Set the title for the registration page */
        $data = ['title' => 'Register Page'];

        /* Render the 'signup' view with the provided data */
        return view('signup', $data);
    }

    /**
     * Handle the user registration.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
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
            /* Find the 'Owner' role */
            $role = Roles::where('role_name', 'Owner')->first();

            /* Handle case where no role is found */
            if (empty($role)) {
                return redirect()->back()->withErrors(['error' => 'Something went wrong'])->withInput();
            }

            /* Create a new team with the provided company name */
            $team = Teams::create([
                'team_name' => $request->input('company')
            ]);

            /* Create a new user with the provided details */
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'team_id' => $team->id
            ]);

            /* Create a new assigned seat for the user */
            $assignedSeat = AssignedSeats::create([
                'user_id' => $user->id,
                'role_id' => $role->id,
                'seat_id' => 0,
            ]);

            /* Create a new manage payment system for the user */
            $manage_payment_system = GlobalPermission::create([
                'permission_name' => 'Manage payment system',
                'permission_slug' => 'manage_payment_system',
                'user_id' => $user->id,
                'team_id' => $team->id,
                'access' => 1,
            ]);

            /* Create a new manage global blacklist for the user */
            $manage_global_blacklist = GlobalPermission::create([
                'permission_name' => 'Manage global blacklist',
                'permission_slug' => 'manage_global_blacklist',
                'user_id' => $user->id,
                'team_id' => $team->id,
                'access' => 1,
            ]);

            /* Commit the transaction */
            DB::commit();

            /* Send a welcome email to the new user */
            Mail::to($user->email)->send(new WelcomeMail($user));

            /* Redirect to login page with a success message */
            return redirect()->route('login')->with('success', 'User registered successfully');
        } catch (\Exception $e) {
            /* Rollback the transaction if something fails */
            DB::rollBack();

            /* Delete created entities only if they exist */
            if (!empty($assignedSeat) && !empty($assignedSeat->id)) {
                $assignedSeat->delete();
            }
            if (!empty($user) && !empty($user->id)) {
                $user->delete();
            }
            if (!empty($team) && !empty($team->id)) {
                $team->delete();
            }
            if (!empty($manage_payment_system) && !empty($manage_payment_system->id)) {
                $manage_payment_system->delete();
            }
            if (!empty($manage_global_blacklist) && !empty($manage_global_blacklist->id)) {
                $manage_global_blacklist->delete();
            }

            /* Log the exception message for debugging */
            Log::error($e);

            /* Redirect back with an error message */
            return redirect()->back()->withErrors(['error' => 'Something went wrong'])->withInput();
        }
    }

    /**
     * Verify the user's email address.
     *
     * @param string $email
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyAnEmail($email)
    {
        try {
            /* Attempt to find the user by their email address */
            $user = User::where('email', $email)->first();

            /* Check if the user was found */
            if (empty($user)) {
                throw new Exception('Something went wrong');
            }

            /* Check if the email has already been verified */
            if (!empty($user->email_verified_at)) {
                /* Redirect to login with a success message if already verified */
                return redirect()->route('login')->with(['success' => 'Email already verified', 'email' => $user->email]);
            }

            /* Set the email_verified_at timestamp to the current time */
            $user->email_verified_at = now();
            $user->save();

            /* Redirect to login with a success message indicating verification was successful */
            return redirect()->route('login')->with(['success' => 'Email Verification Successful', 'email' => $user->email]);
        } catch (\Exception $e) {
            /* Log the exception message for debugging purposes */
            Log::error($e);

            /* Redirect to login with an error message */
            return redirect()->route('login')->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Resend the welcome email to the currently authenticated user.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resend_an_email()
    {
        /* Retrieve the currently authenticated user */
        $user = Auth::user();

        /* Ensure that a user is authenticated before attempting to send an email */
        if ($user) {
            try {
                /* Send a welcome email to the new user */
                Mail::to($user->email)->send(new WelcomeMail($user));

                /* Optionally, you can add a success message or redirect */
                return redirect()->back()->with('success', 'Email sent successfully.');
            } catch (\Exception $e) {
                /* Log the exception message for debugging purposes */
                Log::error($e);

                /* Redirect to login with an error message */
                return redirect()->route('login')->withErrors(['error' => $e->getMessage()]);
            }
        } else {
            /* Optionally, handle the case where no user is authenticated */
            return redirect()->back()->withErrors(['error' => 'No authenticated user found.']);
        }
    }
}
