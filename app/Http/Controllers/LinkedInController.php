<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use GuzzleHttp\Client;


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

    public function createLinkedinAccount(Request $request)
    {
        // echo $request->email;
        // echo $request->password;
        $client = new \GuzzleHttp\Client([
            'verify' => false,
        ]);

        $username = $request->input('email');
        $password = $request->input('password');

        // Validate input
        if (empty($username) || empty($password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email and Password are required.'
            ], 400);
        }

        try {
            $response = $client->request('POST', 'https://api3.unipile.com:13333/api/v1/accounts?limit=100', [
                'json' => [
                    'provider' => 'LINKEDIN',
                    'username' => $username,
                    'password' => $password,
                ],
                'headers' => [
                    'X-API-KEY' => 'nIPVh9fD.gf1u544lGI2nzyGx8K+nkdaIEnbv+8MkLnm3cSKpmVg=',
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                ],
            ]);

            return response()->json([
                'status' => 'success',
                'data' => json_decode($response->getBody()->getContents(), true)
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            return response()->json([
                'status' => 'error',
                'message' => $responseBodyAsString
            ], $response->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
