<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;



class UnipileController extends Controller
{
    public function get_relations()
    {
        $client = new \GuzzleHttp\Client([
            'verify' => false,
        ]);
        $account_id = "-4oRQlvNQSCc9QdsAmaU0g";
        $url = 'https://api3.unipile.com:13333/api/v1/users/relations' . '?limit=3&account_id=' . $account_id;
        $response = $client->request('GET', $url, [
            'headers' => [
                'X-API-KEY' => 'nIPVh9fD.gf1u544lGI2nzyGx8K+nkdaIEnbv+8MkLnm3cSKpmVg=',
                'accept' => 'application/json',
            ],
        ]);
        $user_relations = json_decode($response->getBody(), true);
        echo '<pre>';
        print_r($user_relations);
        echo '<hr>';
        foreach ($user_relations['items'] as $item) {
            $url = 'https://api3.unipile.com:13333/api/v1/users/' . $item['member_id'] . '?linkedin_api=sales_navigator&linkedin_sections=%2A&account_id=' . $account_id;
            $response = $client->request('GET', $url, [
                'headers' => [
                    'X-API-KEY' => 'nIPVh9fD.gf1u544lGI2nzyGx8K+nkdaIEnbv+8MkLnm3cSKpmVg=',
                    'accept' => 'application/json',
                ],
            ]);
            $profile = json_decode($response->getBody(), true);
            echo '<pre>';
            print_r($profile);
        }
    }

    //   public function handleCallback(Request $request)
    //     {
    //         // Log the entire incoming request
    //         Log::info('Unipile callback received', $request->all());



    //         $accountId = $request->input('account_id');
    //         $status = $request->input('status');
    //         $email = $request->input('name');

    //          Log::info('Account ID:', ['account_id' => $accountId]);
    //          Log::info('Status:', ['status' => $status]);




    //     }

    public function handleCallback(Request $request)
    {
        // Log the entire incoming request
        Log::info('Unipile callback received', $request->all());

        // Extract data from the request
        $accountId = $request->input('account_id');
        $status = $request->input('status');
        $email = $request->input('name');

        Log::info('Account ID:', ['account_id' => $accountId]);
        Log::info('Status:', ['status' => $status]);
        Log::info('Email:', ['email' => $email]);

        // Update the user's account_id based on the email
        $update = DB::table('users')
            ->where('email', $email)
            ->update(['account_id' => $accountId]);

        if ($update) {
            Log::info('Account ID updated successfully for user', ['email' => $email, 'account_id' => $accountId]);
            return response()->json(['status' => 'success']);
        } else {
            Log::error('Failed to update Account ID for user', ['email' => $email]);
            return response()->json(['status' => 'error', 'message' => 'User not found or update failed'], 404);
        }
    }

    public function view_profile(Request $request)
    {
        $all = $request->all();
        $account_id = $all['account_id'];
        $profile_url = $all['profile_url'];
        $x_api_key = $all['x-api-key'];
        $client = new \GuzzleHttp\Client([
            'verify' => false,
        ]);
        if (!$account_id || !$profile_url || !$x_api_key) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }
        if (strpos($profile_url, 'https://www.linkedin.com/company/') === false && strpos($profile_url, 'https://www.linkedin.com/in/') === false && strpos($profile_url, 'https://api1.unipile.com:13141/api/v1/linkedin/company/') === false && strpos($profile_url, 'https://api1.unipile.com:13141/api/v1/users/') === false) {
            return response()->json(['error' => 'Incorrect LinkedIn URL'], 400);
        }
        $profile_url = str_replace('https://www.linkedin.com/company/', 'https://api1.unipile.com:13141/api/v1/linkedin/company/', $profile_url);
        $profile_url = str_replace('https://www.linkedin.com/in/', 'https://api1.unipile.com:13141/api/v1/users/', $profile_url);
        $url = $profile_url . '?account_id=' . $account_id;
        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'X-API-KEY' => $x_api_key,
                    'accept' => 'application/json',
                ],
            ]);
            $user_profile = json_decode($response->getBody(), true);
            if ($user_profile['object'] == 'UserProfile' || $user_profile['object'] == 'CompanyProfile') {
                return response()->json(['user_profile' => $user_profile]);
            } else {
                return response()->json(['error' => 'No profile found'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function invite_to_connect(Request $request)
    {
        $all = $request->all();
        $account_id = $all['account_id'];
        $identifier = $all['identifier'];
        $x_api_key = $all['x-api-key'];
        $message = $all['message'];
        if (!$account_id || !$identifier || !$x_api_key) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }
        $client = new \GuzzleHttp\Client([
            'verify' => false,
        ]);
        $response = $client->request('POST', 'https://api1.unipile.com:13141/api/v1/users/invite', [
            'json' => [
                'provider_id' => $identifier,
                'account_id' => $account_id,
                'message' => $message
            ],
            'headers' => [
                'X-API-KEY' => $x_api_key,
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ],
        ]);
        $invitaion = json_decode($response->getBody(), true);
        if ($invitaion['object'] == 'UserInvitationSent') {
            return response()->json(['invitaion' => $invitaion]);
        } else {
            return response()->json(['error' => 'No profile found'], 400);
        }
    }

    public function message(Request $request)
    {
        $all = $request->all();
        $account_id = $all['account_id'];
        $identifier = $all['identifier'];
        $x_api_key = $all['x-api-key'];
        $message = $all['message'];
        if (!$account_id || !$identifier || !$x_api_key) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }
        $client = new \GuzzleHttp\Client([
            'verify' => false,
        ]);
        $response = $client->request('POST', 'https://api1.unipile.com:13141/api/v1/chats', [
            'multipart' => [
                [
                    'name' => 'attendees_ids',
                    'contents' => $identifier
                ],
                [
                    'name' => 'account_id',
                    'contents' => $account_id
                ],
                [
                    'name' => 'text',
                    'contents' => $message
                ]
            ],
            'headers' => [
                'X-API-KEY' => $x_api_key,
                'accept' => 'application/json',
            ],
        ]);
        $message = json_decode($response->getBody(), true);
        return response()->json(['message' => $message]);
    }

    public function inmail_message(Request $request)
    {
        $all = $request->all();
        $account_id = $all['account_id'];
        $identifier = $all['identifier'];
        $x_api_key = $all['x-api-key'];
        $message = $all['message'];
        if (!$account_id || !$identifier || !$x_api_key) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }
        $client = new \GuzzleHttp\Client([
            'verify' => false,
        ]);
        $url = 'https://api1.unipile.com:13141/api/v1/users/me?account_id=' . $account_id;
        $response = $client->request('GET', $url, [
            'headers' => [
                'X-API-KEY' => $x_api_key,
                'accept' => 'application/json',
            ],
        ]);
        $profile = json_decode($response->getBody(), true);
        if ($profile['object'] == 'AccountOwnerProfile' && $profile['premium']) {
            $response = $client->request('POST', 'https://api1.unipile.com:13141/api/v1/chats', [
                'multipart' => [
                    [
                        'name' => 'attendees_ids',
                        'contents' => $identifier
                    ],
                    [
                        'name' => 'inmail',
                        'contents' => 'true'
                    ],
                    [
                        'name' => 'account_id',
                        'contents' => $account_id
                    ],
                    [
                        'name' => 'text',
                        'contents' => $message
                    ]
                ],
                'headers' => [
                    'X-API-KEY' => $x_api_key,
                    'accept' => 'application/json',
                ],
            ]);
            $inmail_message = json_decode($response->getBody(), true);
            return response()->json(['inmail_message' => $inmail_message]);
        } else {
            return response()->json(['error' => 'For this feature must have premium account'], 400);
        }
    }
}
