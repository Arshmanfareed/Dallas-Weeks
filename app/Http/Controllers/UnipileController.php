<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class UnipileController extends Controller
{
    public function get_relations()
    {
        $client = new \GuzzleHttp\Client([
            'verify' => false,
        ]);
        $account_id = "Lxrikq1RSDmt8VG2e1b-RQ";
        $url = 'https://api1.unipile.com:13141/api/v1/linkedin/company/tekunity-pvt-ltd' . '?account_id=' . $account_id;
        $response = $client->request('GET', $url, [
            'headers' => [
                'X-API-KEY' => 'pxVGUgRQ.HxvCCspsvCd+mEBc7C0A3MmQd9b1SV72yiifg1PmM/Y=',
                'accept' => 'application/json',
            ],
        ]);
        $user_profile = json_decode($response->getBody(), true);
        dd($user_profile);
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

    public function email(Request $request)
    {
        $all = $request->all();
        $account_id = $all['account_id'];
        $identifier = $all['identifier'];
        $x_api_key = $all['x-api-key'];
    }
}
