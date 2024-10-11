<?php

namespace App\Http\Controllers;

use App\Models\Blacklist;
use App\Models\Email_Blacklist;
use App\Models\Global_Blacklist;
use App\Models\GlobalPermission;
use App\Models\Teams;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlacklistController extends Controller
{
    /**
     * Display the blacklist for the authenticated user.
     *
     * This method handles the logic for displaying the blacklist for the currently authenticated user. It includes:
     * - Retrieving the authenticated user and their associated team.
     * - Fetching blacklist items for the user and ordering them by creation date.
     * - Checking user permissions for managing the global blacklist and payment system.
     * - Preparing data for the view and handling exceptions by logging errors and redirecting with an error message.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    function blacklist()
    {
        try {
            /* Retrieve the currently authenticated user */
            $user = Auth::user();

            /* Retrieve the team associated with the user. */
            $team = Teams::find($user->team_id);

            /* Fetch the blacklist items for the user, ordered by creation date descending. */
            $blacklist = Blacklist::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            /* Check if the user has permission to manage the global blacklist. */
            $manage_global_blacklist = GlobalPermission::where('permission_slug', 'manage_global_blacklist')
                ->where('user_id', $user->id)
                ->where('team_id', $team->id)
                ->first();
            if (empty($manage_global_blacklist)) {
                /* Redirect if the user does not have permission to manage the global blacklist. */
                return redirect()->route('dashobardz')->withErrors(['error' => "You don't have access to blacklist"]);
            }

            /* Check if the user has permission to manage the payment system. */
            $is_manage_payment_system = false;
            $manage_payment_system = GlobalPermission::where('permission_slug', 'manage_payment_system')
                ->where('user_id', $user->id)
                ->where('team_id', $team->id)
                ->first();
            if (!empty($manage_payment_system)) {
                $is_manage_payment_system = true;
            }

            /* Check if the user has permission to manage the payment system. */
            $is_manage_global_blacklist = false;
            $manage_global_blacklist = GlobalPermission::where('permission_slug', 'manage_global_blacklist')
                ->where('user_id', $user->id)
                ->where('team_id', $team->id)
                ->first();
            if (!empty($manage_global_blacklist)) {
                $is_manage_global_blacklist = true;
            }

            /* Retrieve blacklists */
            $global_blacklist = Global_Blacklist::where('team_id', $team->id)->get();
            $email_blaklist = Email_Blacklist::where('team_id', $team->id)->get();

            /* Prepare data for the view */
            $data = [
                'title' => 'Blacklist',
                'blacklist' => $blacklist,
                'is_manage_payment_system' => $is_manage_payment_system,
                'is_manage_global_blacklist' => $is_manage_global_blacklist,
                'global_blacklist' => $global_blacklist,
                'email_blaklist' => $email_blaklist,
                'team' => $team,
                'is_verified' => !empty(auth()->user()->email_verified_at)
            ];

            /* Return the view with the prepared data */
            return view('blacklist', $data);
        } catch (Exception $e) {
            /* Log the exception message for debugging purposes */
            Log::info($e);

            /* Redirect to login with an error message */
            return redirect('login')->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function deleteGlobalBlacklist($id)
    {
        /* Retrieve the currently authenticated user */
        $user = Auth::user();

        /* Retrieve the team associated with the user. */
        $team = Teams::find($user->team_id);

        /* Find the blacklist item by ID */
        $blacklistItem = Global_Blacklist::find($id);

        /* Check if the blacklist item exists and belongs to the team */
        if (!empty($blacklistItem) && $blacklistItem->team_id == $team->id) {
            /* Delete the blacklist item */
            $blacklistItem->delete();

            /* Return a success response */
            return response()->json(['success' => 'Blacklist item deleted successfully.']);
        }

        /* Return a 404 Not Found response if the item does not exist */
        return response()->json(['error' => 'Blacklist item not found.'], 404);
    }

    public function deleteEmailBlacklist($id)
    {
        /* Retrieve the currently authenticated user */
        $user = Auth::user();

        /* Retrieve the team associated with the user. */
        $team = Teams::find($user->team_id);

        /* Find the blacklist item by ID */
        $blacklistItem = Email_Blacklist::find($id);

        /* Check if the blacklist item exists and belongs to the team */
        if (!empty($blacklistItem) && $blacklistItem->team_id == $team->id) {
            /* Delete the blacklist item */
            $blacklistItem->delete();

            /* Return a success response */
            return response()->json(['success' => 'Blacklist item deleted successfully.']);
        }

        /* Return a 404 Not Found response if the item does not exist */
        return response()->json(['error' => 'Blacklist item not found.'], 404);
    }

    /**
     * Save the global blacklist items for the authenticated user and team.
     *
     * @param  String  $slug
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function saveGlobalBlacklist(Request $request)
    {
        try {
            /* Retrieve the currently authenticated user */
            $creator = Auth::user();

            /* Retrieve the team associated with the user. */
            $team = Teams::find($creator->team_id);

            /* Validate request data */
            $validator = Validator::make($request->all(), [
                'global_blacklist_item' => 'required|array|min:1',
                'global_blacklist_item.*' => 'string|max:255',
                'global_blacklist_type' => 'required|string',
                'global_comparison_type' => 'required|string',
            ]);

            /* Return validation errors if validation fails */
            if ($validator->fails()) {
                return back()->withErrors($validator)
                    ->with('global_blacklist_error', true)
                    ->withInput();
            }

            /* Additional validation for 'profile_url' type */
            if ($request->input('global_blacklist_type') == 'profile_url') {
                if ($request->input('global_comparison_type') !== 'exact') {
                    return back()->withErrors([
                        'global_comparison_type' => 'If Profile Urls is selected so comparison type must be exact',
                    ])
                        ->with('global_blacklist_error', true)
                        ->withInput();
                }

                foreach ($request->input('global_blacklist_item') as $item) {
                    if (strpos($item, 'https://www.linkedin.com/in/') === false) {
                        return back()->withErrors([
                            'global_blacklist_item' => 'Profile URLs must contain "https://www.linkedin.com/in/"'
                        ])
                            ->with('global_blacklist_error', true)
                            ->withInput();
                    }
                }
            }

            /* Save each blacklist item to the Global_Blacklist table */
            foreach ($request->input('global_blacklist_item') as $item) {
                Global_Blacklist::create([
                    'creator_id' => $creator->id,
                    'team_id' => $team->id,
                    'keyword' => $item,
                    'blacklist_type' => $request->input('global_blacklist_type'),
                    'comparison_type' => $request->input('global_comparison_type'),
                ]);
            }

            /* Redirect back to the global blacklist page */
            return redirect()->route('global_blacklist');
        } catch (Exception $e) {
            /* Log the exception message for debugging purposes */
            Log::error($e);

            /* Redirect to dashboard with an error message */
            return redirect()->route('dashobardz')
                ->withErrors(['error' => 'Something went wrong']);
        }
    }

    /**
     * Save the email blacklist items for the authenticated user and team.
     *
     * @param  String  $slug
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function saveEmailBlacklist(Request $request)
    {
        try {
            /* Retrieve the currently authenticated user */
            $creator = Auth::user();

            /* Retrieve the team associated with the user. */
            $team = Teams::find($creator->team_id);

            /* Validate request data */
            $validator = Validator::make($request->all(), [
                'email_blacklist_item' => 'required|array|min:1',
                'email_blacklist_item.*' => 'string|max:255',
                'email_blacklist_type' => 'required|string',
                'email_comparison_type' => 'required|string',
            ]);

            /* Return validation errors if validation fails */
            if ($validator->fails()) {
                return back()->withErrors($validator)
                    ->with('email_blacklist_error', true)
                    ->withInput();
            }

            /* Save each blacklist item to the Global_Blacklist table */
            foreach ($request->input('email_blacklist_item') as $item) {
                Email_Blacklist::create([
                    'creator_id' => $creator->id,
                    'team_id' => $team->id,
                    'keyword' => $item,
                    'blacklist_type' => $request->input('email_blacklist_type'),
                    'comparison_type' => $request->input('email_comparison_type'),
                ]);
            }

            /* Redirect back to the global blacklist page */
            return redirect()->route('global_blacklist');
        } catch (Exception $e) {
            /* Log the exception message for debugging purposes */
            Log::error($e);

            /* Redirect to dashboard with an error message */
            return redirect()->route('dashobardz')->withErrors(['error' => 'Something went wrong']);
        }
    }
}
