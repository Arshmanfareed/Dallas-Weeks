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
     * Display the registration page.
     * 
     * This function sets the title for the registration page and renders the 
     * 'signup' view with the provided data.
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
     * Register a new user and create associated records like team, assigned seat, and permissions.
     * 
     * This function handles the registration of a new user by:
     * - Validating the request data (name, email, password, company, terms agreement)
     * - Starting a database transaction to ensure all operations succeed or fail together
     * - Creating the team, user, assigned seat, and assigning global permissions
     * - Sending a welcome email to the new user
     * - Rolling back the transaction and cleaning up created records if any operation fails
     * 
     * @param Request $request The incoming HTTP request containing user registration details.
     * @return RedirectResponse Redirects to the login page on success or back to the registration form on failure.
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

        /* Use a database transaction to ensure all operations succeed or fail together */
        DB::beginTransaction();

        try {
            /* Retrieve the 'Owner' role from the database */
            $role = Roles::where('role_name', 'Owner')->firstOrFail();

            /* Create a new team with the provided company name */
            $team = Teams::create([
                'team_name' => $request->input('company')
            ]);

            /* Create a new user with the provided name, email, and hashed password */
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'team_id' => $team->id
            ]);

            /* Create an assigned seat for the user with a default seat_id of 0 */
            $assignedSeat = AssignedSeats::create([
                'user_id' => $user->id,
                'role_id' => $role->id,
                'seat_id' => 0,
            ]);

            /* Define permissions to be created for the user */
            $permissions = [
                ['permission_name' => 'Manage payment system', 'permission_slug' => 'manage_payment_system'],
                ['permission_name' => 'Manage global blacklist', 'permission_slug' => 'manage_global_blacklist']
            ];

            $deleteable_permissions = [];

            /* Bulk insert the permissions for better efficiency */
            foreach ($permissions as $permission) {
                $deleteable_permissions[] = GlobalPermission::create([
                    'permission_name' => $permission['permission_name'],
                    'permission_slug' => $permission['permission_slug'],
                    'user_id' => $user->id,
                    'team_id' => $team->id,
                    'access' => 1
                ]);
            }

            /* Commit the transaction as all operations succeeded */
            DB::commit();

            /* Send a welcome email to the newly registered user */
            Mail::to($user->email)->send(new WelcomeMail($user));

            /* Redirect to the login page with a success message */
            return redirect()->route('login')->with('success', 'User registered successfully');
        } catch (\Exception $e) {
            /* Rollback the transaction if something fails */
            DB::rollBack();

            /* Delete created entities only if they exist */
            if (!empty($deleteable_permission)) {
                foreach ($deleteable_permissions as $deleteable_permission) {
                    if (!empty($deleteable_permission) && !empty($deleteable_permission->id)) {
                        $deleteable_permission->delete();
                    }
                }
            }
            if (!empty($assignedSeat) && !empty($assignedSeat->id)) {
                $assignedSeat->delete();
            }
            if (!empty($user) && !empty($user->id)) {
                $user->delete();
            }
            if (!empty($team) && !empty($team->id)) {
                $team->delete();
            }

            /* Log the exception message for debugging */
            Log::error($e);

            /* Redirect back with an error message */
            return redirect()->back()->withErrors(['error' => 'Something went wrong'])->withInput();
        }
    }

    /**
     * Resend a welcome email to the currently authenticated user.
     * 
     * This function is responsible for:
     * - Retrieving the currently authenticated user
     * - Sending a welcome email to the user's registered email address
     * - Handling potential errors during the email sending process and logging them for debugging
     * - Returning appropriate success or error responses depending on the outcome
     * 
     * @return RedirectResponse Redirects back with a success message if the email is sent successfully, 
     * or with an error message if something goes wrong.
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
                return redirect()->route('login')->withErrors(['error' => 'Something went wrong']);
            }
        } else {
            /* Optionally, handle the case where no user is authenticated */
            return redirect()->back()->withErrors(['error' => 'No authenticated user found.']);
        }
    }

    /**
     * Verify the email address of a user.
     *
     * This method attempts to verify a user's email address by setting the `email_verified_at` field to the current timestamp.
     * If the email address is already verified or if the user cannot be found, appropriate messages are returned.
     * In case of an error, the exception is logged, and an error message is shown to the user.
     *
     * @param  string  $email  The email address to verify.
     * @return \Illuminate\Http\RedirectResponse  Redirects to the login page with either a success or error message.
     */
    public function verifyAnEmail($email)
    {
        try {
            /* Attempt to find the user by their email address */
            $user = User::where('email', $email)->first();

            /* Check if the user was found */
            if (empty($user)) {
                throw new Exception('User not found');
            }

            /* Check if the email has already been verified */
            if (!empty($user->email_verified_at)) {
                /* If the email is already verified, redirect to the login page with a success message. */
                return redirect()->route('login')->with([
                    'success' => 'Email already verified',
                    'email' => $user->email
                ]);
            }

            /* If the email is not verified, set the `email_verified_at` field to the current timestamp. */
            $user->email_verified_at = now();
            $user->updated_at = now();
            $user->save();

            /* Redirect to the login page with a success message indicating that the email verification was successful. */
            return redirect()->route('login')->with([
                'success' => 'Email Verification Successful',
                'email' => $user->email
            ]);
        } catch (\Exception $e) {
            /* If an exception occurs, log the exception message for debugging purposes. */
            Log::error($e);

            /* Redirect to the login page with an error message indicating that something went wrong. */
            return redirect()->route('login')->withErrors(['error' => 'Something went wrong']);
        }
    }
}
