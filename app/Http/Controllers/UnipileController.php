<?php

namespace App\Http\Controllers;

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
}