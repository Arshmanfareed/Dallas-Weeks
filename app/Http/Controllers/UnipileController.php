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
        $account_id = "k0TSnqL4RwSGpWPEGtZuKg";
        $url = 'https://api1.unipile.com:13141/api/v1/users/relations' . '?limit:2&account_id=' . $account_id;
        $response = $client->request('GET', $url, [
            'headers' => [
                'X-API-KEY' => 'YU/XcHpQ.XUmTQ6X8kSKffj6fqOUaU/IevKUojLoRzeYlObycvtQ=',
                'accept' => 'application/json',
            ],
        ]);
        $user_relations = json_decode($response->getBody(), true);
        foreach ($user_relations['items'] as $item) {
            $url = 'https://api1.unipile.com:13141/api/v1/users/' . $item['public_identifier'] . '?linkedin_sections=%2A&account_id=' . $account_id;
            $response = $client->request('GET', $url, [
                'headers' => [
                    'X-API-KEY' => 'YU/XcHpQ.XUmTQ6X8kSKffj6fqOUaU/IevKUojLoRzeYlObycvtQ=',
                    'accept' => 'application/json',
                ],
            ]);
            $profile = json_decode($response->getBody(), true);
            echo '<pre>';
            print_r($profile['contact_info']['emails']);
        }
    }
}
