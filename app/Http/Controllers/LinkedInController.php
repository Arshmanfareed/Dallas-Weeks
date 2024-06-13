<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\SeatInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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
    
    public function createLinkAccount(Request $request)
    {
        $all = $request->all();
        $email = $all['email'];
        $client = new \GuzzleHttp\Client([
            'verify' => false,
        ]);
        try {
            $response = $client->request('POST', 'https://api2.unipile.com:13225/api/v1/hosted/accounts/link', [
                'json' => [
                    'type' => 'create',
                    'providers' => '*',
                    'api_url' => 'https://api2.unipile.com:13225',
                    'expiresOn' => '2024-12-22T12:00:00.701Z',
                    'success_redirect_url' => 'https://networked.staging.designinternal.com/setting',
                    'failure_redirect_url' => 'https://networked.staging.designinternal.com/setting',
                    'notify_url' => 'https://networked.staging.designinternal.com/unipile-callback',
                    'name' => $email,
                ],
                'headers' => [
                    'X-API-KEY' => 'BN0rHqQh.rpWV9jWRAH6ZdCklpjQyfoec3DQ3PWFfYXVHMuUNN5E=',
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
    
    public function delete_an_account()
    {
        if (Auth::check()) {
            if (session()->has('seat_id')) {
                $seat_id = session('seat_id');
                $seat = SeatInfo::where('id', $seat_id)->first();
                if ($seat['account_id'] !== NULL) {
                    $request = [
                        'account_id' => $seat['account_id'],
                    ];
                    $uc = new UnipileController();
                    $account = $uc->delete_account(new \Illuminate\Http\Request($request));
                    if ($account instanceof JsonResponse) {
                        $account = $account->getData(true);
                        if (!isset($account['error'])) {
                            $seat['account_id'] = null;
                            $seat->save();
                            session(['delete_account' => true]);
                            return response()->json(['success' => true]);
                        } else {
                            return response()->json(['success' => false, 'error' => $account['error']]);
                        }
                    } else {
                        return response()->json(['success' => false]);
                    }
                } else {
                    session(['add_account' => true]);
                    return redirect(route('dash-settings'));
                }
            } else {
                return redirect(route('dashobardz'));
            }
        } else {
            return redirect(url('/'));
        }
    }
}
