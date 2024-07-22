<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Psr7\Request as IlluminateRequest;

class UnipileController extends Controller
{
    var $x_api_key = 'Z+eeumbS.GmXz1XXr2mxTXjEsn9vepK/2xnq+HcR8bpoGSuv/l6w=';
    var $dsn = 'https://api4.unipile.com:13443/';

    public function get_accounts()
    {
        $client = new \GuzzleHttp\Client([
            'verify' => false,
        ]);
        if (!isset($this->x_api_key) || !isset($this->dsn)) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }
        try {
            $response = $client->request('GET', $this->dsn . 'api/v1/accounts', [
                'headers' => [
                    'X-API-KEY' => $this->x_api_key,
                    'accept' => 'application/json',
                ],
            ]);

            $accounts = json_decode($response->getBody(), true);
            return response()->json(['accounts' => $accounts]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function retrieve_an_account(Request $request)
    {
        $all = $request->all();
        $account_id = $all['account_id'];
        $client = new \GuzzleHttp\Client([
            'verify' => false,
        ]);
        if (!isset($account_id) || !isset($this->x_api_key) || !isset($this->dsn)) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }
        $url = $this->dsn . 'api/v1/accounts/' . $account_id;
        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'X-API-KEY' => $this->x_api_key,
                    'accept' => 'application/json',
                ],
            ]);
            $account = json_decode($response->getBody(), true);
            return response()->json(['account' => $account]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function get_relations(Request $request)
    {
        $all = $request->all();
        $account_id = $all['account_id'];
        $client = new \GuzzleHttp\Client([
            'verify' => false,
        ]);
        if (!isset($account_id) || !isset($this->x_api_key) || !isset($this->dsn)) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }
        $url = $this->dsn . 'api/v1/users/relations' . '?account_id=' . $account_id;
        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'X-API-KEY' => $this->x_api_key,
                    'accept' => 'application/json',
                ],
            ]);
            $responses = json_decode($response->getBody(), true);
            $relations = array();
            if (!empty($responses)) {
                foreach ($responses['items'] as $response) {
                    $url = '';
                    if ($response['object'] == 'UserRelation') {
                        $url = $this->dsn . 'api/v1/users/' . $response['member_id'];
                    } elseif ($response['object'] == 'CompanyProfile') {
                        $url = '' . $response[''];
                    }
                    $profile = [
                        'account_id' => $account_id,
                        'profile_url' => $url
                    ];
                    $relations[] = $this->view_profile(new \Illuminate\Http\Request($profile))->getData(true)['user_profile'];
                }
                return response()->json(['relations' => $relations]);
            } else {
                return response()->json(['error' => 'No relations found'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function sales_navigator_search(Request $request)
    {
        $all = $request->all();
        $query = $all['query'];
        $account_id = $all['account_id'];
        $count = 80;
        $start = 0;
        if (isset($all['count']) && $all['count'] < 80) {
            $count = $all['count'];
        }
        if (isset($all['start'])) {
            $start = $all['start'];
        }
        $client = new Client([
            'verify' => false,
        ]);
        if (!isset($account_id) || !isset($this->x_api_key) || !isset($this->dsn)) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }
        try {
            $response = $client->request('POST', $this->dsn . 'api/v1/linkedin', [
                'json' => [
                    'query_params' => [
                        'decorationId' => 'com.linkedin.sales.deco.desktop.searchv2.LeadSearchResult-14',
                        'query' => $query,
                        'count' => $count,
                        'start' => $start,
                        'q' => 'searchQuery',
                    ],
                    'account_id' => $account_id,
                    'method' => 'GET',
                    'request_url' => 'https://www.linkedin.com/sales-api/salesApiLeadSearch',
                    'encoding' => false
                ],
                'headers' => [
                    'X-API-KEY' => $this->x_api_key,
                    'accept' => 'application/json',
                    'content-type' => 'application/json'
                ],
            ]);
            $result = json_decode($response->getBody(), true);
            return response()->json(['accounts' => $result['data']['elements']]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    private function queryToString($query)
    {
        $string = '';
        foreach ($query as $key => $value) {
            $string .= '(key:' . $key . ',value:List(';
            if (is_array($value)) {
                $string .= implode(',', $value);
            } else {
                $string .= $value;
            }
            $string .= ')),';
        }
        $string = rtrim($string, ',');
        return $string;
    }

    public function linkedin_search(Request $request)
    {
        $all = $request->all();
        $query = $all['query'];
        $account_id = $all['account_id'];
        $start = 0;
        $origin = 'FACETED_SEARCH';
        $keywords = '';
        $queryParams = '';
        if (isset($all['start'])) {
            $start = $all['start'];
        }
        if (isset($query['origin'])) {
            $origin = $query['origin'];
            unset($query['origin']);
        }
        if (isset($query['keywords'])) {
            $keywords = 'keywords:' . $query['keywords'] . ',';
            unset($query['keywords']);
        }
        if (!empty($query)) {
            $queryParams = $this->queryToString($query);
        }
        $client = new Client([
            'verify' => false,
        ]);
        if (!isset($account_id) || !isset($this->x_api_key) || !isset($this->dsn)) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }
        try {
            $response = $client->request('POST', $this->dsn . 'api/v1/linkedin', [
                'json' => [
                    'query_params' => [
                        'variables' => '(start:' . $start . ',origin:' . $origin . ',query:(' . $keywords . 'flagshipSearchIntent:SEARCH_SRP,queryParameters:List(' . $queryParams . ',(key:resultType,value:List(PEOPLE))),includeFiltersInResponse:false))',
                        'queryId' => 'voyagerSearchDashClusters.838ad2ecdec3b0347f493f93602336e9'
                    ],
                    'account_id' => $account_id,
                    'method' => 'GET',
                    'request_url' => 'https://www.linkedin.com/voyager/api/graphql',
                    'encoding' => false
                ],
                'headers' => [
                    'X-API-KEY' => $this->x_api_key,
                    'accept' => 'application/json',
                    'content-type' => 'application/json'
                ],
            ]);
            $result = json_decode($response->getBody(), true);
            return response()->json(['accounts' => $result['data']['data']['searchDashClustersByAll']['elements']]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function post_search(Request $request)
    {
        $all = $request->all();
        $account_id = $all['account_id'];
        $identifier = $all['identifier'];
        if (!isset($account_id) || !isset($identifier) || !isset($this->x_api_key) || !isset($this->dsn)) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }
        $client = new Client([
            'verify' => false,
        ]);
        $url = $this->dsn . 'api/v1/posts/' . $identifier . '?account_id=' . $account_id;
        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'X-API-KEY' => $this->x_api_key,
                    'accept' => 'application/json',
                ],
            ]);
            $result = json_decode($response->getBody(), true);
            return response()->json(['post' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function reactions_post_search(Request $request)
    {
        $all = $request->all();
        $account_id = $all['account_id'];
        $identifier = $all['identifier'];
        if (!isset($account_id) || !isset($identifier) || !isset($this->x_api_key) || !isset($this->dsn)) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }
        $client = new Client([
            'verify' => false,
        ]);
        $url = $this->dsn . 'api/v1/posts/' . $identifier . '/reactions?account_id=' . $account_id;
        if (isset($all['cursor'])) {
            $url .= '&cursor=' . $all['cursor'];
        }
        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'X-API-KEY' => $this->x_api_key,
                    'accept' => 'application/json',
                ],
            ]);
            $result = json_decode($response->getBody(), true);
            return response()->json(['reactions' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function delete_account(Request $request)
    {
        $all = $request->all();
        $account_id = $all['account_id'];
        $client = new \GuzzleHttp\Client([
            'verify' => false,
        ]);
        if (!isset($account_id) || !isset($this->x_api_key) || !isset($this->dsn)) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }
        $url = $this->dsn . 'api/v1/accounts/' . $account_id;
        try {
            $response = $client->request('DELETE', $url, [
                'headers' => [
                    'X-API-KEY' => $this->x_api_key,
                    'accept' => 'application/json',
                ],
            ]);
            $delete_account = json_decode($response->getBody(), true);
            return response()->json(['account' => $delete_account]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function handleCallback(Request $request)
    {
        Log::info('Unipile callback received', $request->all());

        $accountId = $request->input('account_id');
        $status = $request->input('status');
        $email = $request->input('name');

        Log::info('Account ID:', ['account_id' => $accountId]);
        Log::info('Status:', ['status' => $status]);
        Log::info('Email:', ['email' => $email]);

        $update = DB::table('seat_info')
            ->where('id', $email)
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
        $notify = 'false';
        if (isset($all['notify'])) {
            $notify = 'true';
        }
        $client = new \GuzzleHttp\Client([
            'verify' => false,
        ]);
        if (!isset($account_id) || !isset($profile_url) || !isset($this->x_api_key) || !isset($this->dsn)) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }
        if (isset($all['sales_navigator'])) {
            $url =  $this->dsn . 'api/v1/users/' . $profile_url . '?linkedin_api=sales_navigator&linkedin_sections=%2A&notify=' . $notify . '&account_id=' . $account_id;
        } else {
            $profile_url = str_replace('https://www.linkedin.com/company/', $this->dsn . 'api/v1/linkedin/company/', $profile_url);
            $profile_url = str_replace('https://www.linkedin.com/in/', $this->dsn . 'api/v1/users/', $profile_url);
            $url = $profile_url . '?linkedin_sections=%2A&notify=' . $notify . '&account_id=' . $account_id;
        }
        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'X-API-KEY' => $this->x_api_key,
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
        if (!isset($account_id) || !isset($identifier) || !isset($this->x_api_key) || !isset($this->dsn)) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }
        if (isset($all['message'])) {
            $message = $all['message'];
        } else {
            $message = '';
        }
        $client = new \GuzzleHttp\Client([
            'verify' => false,
        ]);
        try {
            $response = $client->request('POST', $this->dsn . 'api/v1/users/invite', [
                'json' => [
                    'provider_id' => $identifier,
                    'account_id' => $account_id,
                    'message' => $message
                ],
                'headers' => [
                    'X-API-KEY' => $this->x_api_key,
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
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function message(Request $request)
    {
        $all = $request->all();
        $account_id = $all['account_id'];
        $identifier = $all['identifier'];
        if (!isset($account_id) || !isset($identifier) || !isset($this->x_api_key) || !isset($this->dsn)) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }
        if (isset($all['message'])) {
            $message = $all['message'];
        } else {
            $message = '';
        }
        $client = new \GuzzleHttp\Client([
            'verify' => false,
        ]);
        try {
            $response = $client->request('POST', $this->dsn . 'api/v1/chats', [
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
                    'X-API-KEY' => $this->x_api_key,
                    'accept' => 'application/json',
                ],
            ]);
            $message = json_decode($response->getBody(), true);
            return response()->json(['message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function inmail_message(Request $request)
    {
        $all = $request->all();
        $account_id = $all['account_id'];
        $identifier = $all['identifier'];
        if (isset($all['message'])) {
            $message = $all['message'];
        } else {
            $message = '';
        }
        if (!isset($account_id) || !isset($identifier) || !isset($this->x_api_key) || !isset($this->dsn)) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }
        $client = new \GuzzleHttp\Client([
            'verify' => false,
        ]);
        $url = $this->dsn . 'api/v1/users/me?account_id=' . $account_id;
        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'X-API-KEY' => $this->x_api_key,
                    'accept' => 'application/json',
                ],
            ]);
            $profile = json_decode($response->getBody(), true);
            if ($profile['object'] == 'AccountOwnerProfile' && $profile['premium']) {
                $response = $client->request('POST', $this->dsn . 'api/v1/chats', [
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
                        'X-API-KEY' => $this->x_api_key,
                        'accept' => 'application/json',
                    ],
                ]);
                $inmail_message = json_decode($response->getBody(), true);
                return response()->json(['inmail_message' => $inmail_message]);
            } else {
                return response()->json(['error' => 'For this feature must have premium account'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function email_message(Request $request)
    {
        $all = $request->all();
        $account_id = $all['account_id'];
        $email = $all['email'];
        if (!isset($account_id) || !isset($email) || !isset($this->x_api_key) || !isset($this->dsn)) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }
        if (isset($all['subject'])) {
            $subject = $all['subject'];
        } else {
            $subject = '';
        }
        if (isset($all['message'])) {
            $messageContent = $all['message'];
        } else {
            $messageContent = '';
        }
        try {
            Mail::send([], [], function ($mail) use ($email, $subject, $messageContent) {
                $mail->to($email)
                    ->subject($subject)
                    ->setBody($messageContent, 'text/html');
            });
            return response()->json(['success' => true, 'message' => 'Email sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send email', 'details' => $e->getMessage()], 500);
        }
    }

    public function follow(Request $request)
    {
        $all = $request->all();
        $account_id = $all['account_id'];
        $identifier = $all['identifier'];
        $client = new \GuzzleHttp\Client([
            'verify' => false,
        ]);
        if (!isset($account_id) || !isset($identifier) || !isset($this->x_api_key) || !isset($this->dsn)) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }
        $request_url = "https://www.linkedin.com/voyager/api/feed/dash/followingStates/urn:li:fsd_followingState:urn:li:fsd_profile:" . $identifier;
        try {
            $response = $client->request('POST', $this->dsn . 'api/v1/linkedin', [
                'json' => [
                    'body' => [
                        'patch' => [
                            '$set' => [
                                'following' => true
                            ]
                        ]
                    ],
                    'account_id' => $account_id,
                    'method' => 'POST',
                    'request_url' => $request_url,
                    'encoding' => false
                ],
                'headers' => [
                    'X-API-KEY' => $this->x_api_key,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);
            $follow = json_decode($response->getBody(), true);
            return response()->json(['follow' => $follow]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
