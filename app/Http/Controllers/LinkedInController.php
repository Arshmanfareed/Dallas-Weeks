<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class LinkedInController extends Controller
{
    public function redirectToLinkedIn()
    {
        $state = bin2hex(random_bytes(16)); // Generate a random state

        // Save the state to the session for later verification
        session(['linkedin_state' => $state]);

        $url = 'https://www.linkedin.com/oauth/v2/authorization?' . http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.linkedin.client_id'),
            'redirect_uri' => config('services.linkedin.redirect'),
            'scope' => 'r_liteprofile r_emailaddress',
            'state' => $state,
        ]);

        return redirect()->away($url);
    }

    public function handleLinkedInCallback(Request $request)
    {
        // Verify state to prevent CSRF
        if ($request->state !== session('linkedin_state')) {
            // Handle invalid state
            return redirect()->route('login')->with('error', 'Invalid state parameter');
        }

        // Exchange authorization code for access token
        $response = Http::post('https://www.linkedin.com/oauth/v2/accessToken', [
            'grant_type' => 'authorization_code',
            'code' => $request->code,
            'client_id' => config('services.linkedin.client_id'),
            'client_secret' => config('services.linkedin.client_secret'),
            'redirect_uri' => config('services.linkedin.redirect'),
        ]);

        $data = $response->json();

        // Use $data['access_token'] to make API requests or authenticate the user
        // ...

        return redirect()->route('home')->with('success', 'LinkedIn login successful');
    }
}
