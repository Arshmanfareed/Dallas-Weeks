<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Leads;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

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
    public function handleCallback(Request $request)
    {
        // Log the entire incoming request
        Log::info('Unipile callback received', $request->all());

        // Check if the request is a GET or POST
        if ($request->isMethod('post')) {
            // Validate and extract the data from the request
            $validatedData = $request->validate([
                'account_id' => 'required|string',
                'name' => 'required|string',
                'message' => 'required|string',
            ]);

            $accountId = $validatedData['account_id'];
            $name = $validatedData['name'];
            $message = $validatedData['message'];

            if ($message === 'CREATION_SUCCESS') {
                // Process the account ID and name as needed
                Log::info('Account ID:', ['account_id' => $accountId]);
                Log::info('Name:', ['name' => $name]);
            }

            // Respond to the POST request
            return response()->json(['status' => 'success']);
        } elseif ($request->isMethod('get')) {
            // Handle GET requests (e.g., for redirects)
            return response()->json(['status' => 'GET request received']);
        }

        // If neither, return an error response
        return response()->json(['status' => 'Invalid request method'], 405);
    }
}
