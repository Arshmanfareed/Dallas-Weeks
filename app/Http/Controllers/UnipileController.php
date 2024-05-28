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
        $account_id = "JrayZNtLTY6h9ymbNNgngQ";
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
}
