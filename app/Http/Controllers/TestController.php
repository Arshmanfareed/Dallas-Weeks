<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\SeatInfo;
use App\Models\ImportedLeads;
use App\Models\Leads;
use Illuminate\Http\Request;
use Exception;

use function PHPUnit\Framework\isEmpty;

class TestController extends Controller
{
    public function base()
    {
        try {
            $seats = SeatInfo::whereNotNull('account_id')->get();
            $final_accounts = [];
            $uc = new UnipileController();
            for ($i = 0; $i < count($seats); $i++) {
                $account_id = [
                    'account_id' => $seats[$i]['account_id'],
                ];
                $account = $uc->retrieve_an_account(new \Illuminate\Http\Request($account_id));
                $account = $account->getData(true);
                if (array_key_exists($account['account']['connection_params']['im']['id'], $final_accounts)) {
                    $final_accounts[$account['account']['connection_params']['im']['id']][] = $seats[$i]['account_id'];
                } else {
                    $final_accounts[$account['account']['connection_params']['im']['id']] = [];
                    $final_accounts[$account['account']['connection_params']['im']['id']][] = $seats[$i]['account_id'];
                }
            }
            foreach ($final_accounts as $final_account) {
                $seats = SeatInfo::whereIn('account_id', $final_account)->get();
                $campaigns = Campaign::whereIn('seat_id', $seats->pluck('id')->toArray())->get();
                if (count($campaigns) > 0) {
                    $lead_distribution = floor(80 / count($campaigns));
                    foreach ($campaigns as $campaign) {
                        if ($campaign['campaign_type'] == 'import') {
                            $imported_lead = ImportedLeads::where('user_id', $campaign['user_id'])->where('campaign_id', $campaign['id'])->first();
                            $csvController = new CsvController();
                            $csvData = $csvController->importedLeadToArray($imported_lead['file_path']);
                            $i = 0;
                            if ($csvData !== NULL) {
                                foreach ($csvData as $key => $value) {
                                    if (str_contains(strtolower($key), 'url')) {
                                        foreach ($value as $url) {
                                            if ($i >= $lead_distribution) {
                                                break;
                                            }
                                            $lead = Leads::where('campaign_id', $campaign['id'])->where('profileUrl', $url)->first();
                                            if (isEmpty($lead) && $i < $lead_distribution) {
                                                $lc = new LeadsController();
                                                if ($lc->applySettings($campaign, $url) !== 'Not found') {
                                                    $i++;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            throw new Exception($e);
        }
    }
}
