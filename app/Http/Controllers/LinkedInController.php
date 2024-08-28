<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\SeatInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LinkedInController extends Controller
{
    var $x_api_key = 'Z+eeumbS.GmXz1XXr2mxTXjEsn9vepK/2xnq+HcR8bpoGSuv/l6w=';
    var $dsn = 'https://api4.unipile.com:13443/';

    public function createLinkAccount(Request $request)
    {
        try {
            $all = $request->all();
            $email = $all['email'];
            $provider[] = "LINKEDIN";
            $expirationTime = (new \DateTime())->modify('+15 minutes')->format('Y-m-d\TH:i:s.v\Z');
            $client = new \GuzzleHttp\Client([
                'verify' => false,
            ]);
            $response = $client->request('POST', $this->dsn . 'api/v1/hosted/accounts/link', [
                'json' => [
                    'type' => 'create',
                    'providers' => $provider,
                    'api_url' => $this->dsn,
                    'expiresOn' => $expirationTime,
                    'success_redirect_url' => 'https://networked.staging.designinternal.com/accdashboard',
                    'failure_redirect_url' => 'https://networked.staging.designinternal.com/setting',
                    'notify_url' => 'https://networked.staging.designinternal.com/unipile-callback',
                    'name' => 'linkedin' . $email,
                ],
                'headers' => [
                    'X-API-KEY' => $this->x_api_key,
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                ],
            ]);
            $data = [
                'status' => 'success',
                'data' => json_decode($response->getBody()->getContents(), true)
            ];
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e], 500);
        }
    }

    public function delete_an_account()
    {
        try {
            $seat = SeatInfo::find(session('seat_id'));
            if (!$seat || empty($seat->account_id)) {
                session(['add_account' => true]);
                return redirect()->route('dash-settings');
            }
            $uc = new UnipileController();
            $request = ['account_id' => $seat['account_id']];
            $account = $uc->delete_account(new \Illuminate\Http\Request($request));
            if ($account instanceof JsonResponse) {
                $account = $account->getData(true);
                if (isset($account['error'])) {
                    return response()->json(['success' => false, 'error' => $account['error']]);
                }
                $seat->update(['account_id' => null]);
                session(['delete_account' => true]);
                return response()->json(['success' => true]);
            }
            return response()->json(['success' => false]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e]);
        }
    }

    public function addEmailToAccount(Request $request)
    {
        try {
            $all = $request->all();
            $seat = session('seat_id');
            $provider[] = $all['provider'];
            $expirationTime = (new \DateTime())->modify('+15 minutes')->format('Y-m-d\TH:i:s.v\Z');
            $client = new \GuzzleHttp\Client([
                'verify' => false,
            ]);
            $response = $client->request('POST', $this->dsn . 'api/v1/hosted/accounts/link', [
                'json' => [
                    'type' => 'create',
                    'providers' => $provider,
                    'api_url' => $this->dsn,
                    'expiresOn' => $expirationTime,
                    'success_redirect_url' => 'https://networked.staging.designinternal.com/accdashboard',
                    'failure_redirect_url' => 'https://networked.staging.designinternal.com/setting',
                    'notify_url' => 'https://networked.staging.designinternal.com/unipile-callback',
                    'name' => 'email' . $seat,
                ],
                'headers' => [
                    'X-API-KEY' => $this->x_api_key,
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                ],
            ]);
            $data = [
                'success' => true,
                'data' => json_decode($response->getBody()->getContents(), true)
            ];
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e], 500);
        }
    }
}
