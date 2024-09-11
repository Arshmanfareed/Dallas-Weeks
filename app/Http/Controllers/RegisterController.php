<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\TeamMembers;
use App\Models\Teams;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
            'company' => 'required',
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[!@#$%^&*(),.?":{}|<>]).*$/',
                'confirmed'
            ],
            'termsCheckbox' => 'required'
        ], [
            'password.regex' => 'The password must include at least one special character.',
            'termsCheckbox.required' => 'Terms and conditions must be checked'
        ]);

        /* Return validation errors if validation fails */
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            /* Create a new user with the provided details */
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'phone_number' => $request->input('username_phone') ?? NULL,
                'remember_token' => Str::random(37),
            ]);

            /* Create a new team for the user */
            $team = Teams::create([
                'name' => $request->input('company'),
                'creator_id' => $user->id,
            ]);

            /* Add the user to the team */
            $team_member = TeamMembers::create([
                'user_id' => $user->id,
                'team_id' => $team->id,
            ]);

            /* Send a welcome email to the new user */
            Mail::to($user->email)->send(new WelcomeMail($user));

            /* Redirect to login page with a success message */
            return redirect()->route('login')->with([
                'success' => 'User registered successfully',
                'email' => $user->email,
            ]);
        } catch (\Exception $e) {
            /* Delete created entities only if they exist */
            if (!empty($team) && !empty($team->id)) {
                $team->delete();
            }
            if (!empty($user) && !empty($user->id)) {
                $user->delete();
            }
            if (!empty($team_member) && !empty($team_member->id)) {
                $team_member->delete();
            }

            /* Log the exception message for debugging */
            Log::error($e);

            /* Redirect back with an error message */
            return redirect()->back()->withErrors(['error' => 'Something went wrong'])->withInput();
        }
    }

    /**
     * Resend the welcome email to the currently authenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resend_an_email(Request $request)
    {
        try {
            /* Attempt to find the user by their email address */
            $user = User::where('email', $request->input('user_email'))->firstOrFail();
            $user->remember_token = Str::random(37);
            $user->save();

            /* Send a welcome email to the new user */
            Mail::to($user->email)->send(new WelcomeMail($user));

            /* Redirect to redirect with a success message */
            return redirect()->back()->with([
                'success' => 'Email sent successfully.',
                'email' => $user->email,
            ]);
        } catch (\Exception $e) {
            /* Log the exception message for debugging purposes */
            Log::error($e);

            /* Redirect to login with an error message */
            return redirect()->route('login')->withErrors(['error' => 'Something went wrong']);
        }
    }

    /**
     * Verify the user's email address.
     *
     * @param string $email
     * @param string $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyAnEmail($email, $token)
    {
        try {
            /* Attempt to find the user by their email address */
            $user = User::where('email', $email)->firstOrFail();

            /* Check if the email has already been verified */
            if ($user->email_verified_at !== null) {
                /* Redirect to login with a success message if already verified */
                return redirect()->route('login')->with([
                    'success' => 'Email already verified',
                    'email' => $user->email,
                ]);
            }

            /* Check if the provided token is matched with user token */
            if ($user->remember_token !== $token) {
                /* Redirect to login with a success message if already verified */
                return redirect()->route('login')->with([
                    'email' => $user->email,
                ])->withErrors([
                    'mismatch_token' => 'Email mismatch token',
                ]);
            }

            /* Set the email_verified_at timestamp to the current time */
            $user->email_verified_at = now();
            $user->save();

            /* Redirect to login with a success message indicating verification was successful */
            return redirect()->route('login')->with([
                'success' => 'Email Verification Successful',
                'email' => $user->email,
            ]);
        } catch (\Exception $e) {
            /* Log the exception message for debugging purposes */
            Log::error($e);

            /* Redirect to login with an error message */
            return redirect()->route('login')->withErrors(['error' => $e->getMessage()]);
        }
    }
}
