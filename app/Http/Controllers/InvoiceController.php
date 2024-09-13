<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Teams;
use App\Models\GlobalPermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    /**
     * Display the invoice page for the authenticated user.
     *
     * This method handles the logic for displaying the invoice page. It includes:
     * - Retrieving the authenticated user and their associated team.
     * - Checking user permissions for managing invoices and global blacklist.
     * - Preparing data for the invoice view.
     * - Handling exceptions by logging errors and redirecting with an error message.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    function invoice()
    {
        try {
            /* Retrieve the currently authenticated user */
            $user = Auth::user();

            /* Retrieve the team associated with the user. */
            $team = Teams::find($user->team_id);

            /* Check if the user has permission to manage the payment system. */
            $manage_payment_system = GlobalPermission::where('permission_slug', 'manage_payment_system')
                ->where('user_id', $user->id)
                ->where('team_id', $team->id)
                ->first();
            if (empty($manage_payment_system)) {
                return redirect()->route('dashobardz')->withErrors(['error' => "You don't have access to invoices"]);
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

            /* Check if the user has permission to manage the global blacklist. */
            $is_manage_global_blacklist = false;
            $manage_global_blacklist = GlobalPermission::where('permission_slug', 'manage_global_blacklist')
                ->where('user_id', $user->id)
                ->where('team_id', $team->id)
                ->first();
            if (!empty($manage_global_blacklist)) {
                $is_manage_global_blacklist = true;
            }

            /* Prepare data for the view. */
            $data = [
                'title' => 'Invoices',
                'is_manage_payment_system' => $is_manage_payment_system,
                'is_manage_global_blacklist' => $is_manage_global_blacklist,
            ];

            /* Return the view with the prepared data. */
            return view('invoice', $data);
        } catch (Exception $e) {
            /* Log the exception message for debugging purposes. */
            Log::info($e);

            /* Redirect to login with an error message if an exception occurs. */
            return redirect('login')->withErrors(['error' => $e->getMessage()]);
        }
    }
}
