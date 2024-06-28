<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\SeatInfo;
use App\Models\ImportedLeads;
use Illuminate\Http\Request;
use Exception;

class TestController extends Controller
{
    public function base()
    {
        try {
            $seats = SeatInfo::whereNotNull('account_id')->get();
            foreach ($seats as $seat) {
                $campaigns = Campaign::where('seat_id', $seat['id'])->where('is_active', 1)->where('is_archive', 0)->get();
                $lead_distribution = floor(80 / count($campaigns));
                foreach ($campaigns as $campaign) {
                    $i = 0;
                    if ($campaign['campaign_type'] == 'import') {
                        $imported_lead = ImportedLeads::where('campaign_id', $campaign['id'])->first();
                        $csvController = new CsvController();
                        $csvData = $csvController->importedLeadToArray($imported_lead);
                        if ($csvData !== NULL) {
                            foreach ($csvData as $key => $value) {
                                foreach ($value as $url) {
                                    if (str_contains(strtolower($key), 'url') && $i < $lead_distribution) {
                                        $lc = new LeadsController();
                                        if ($lc->applySettings($campaign['id'], $url) !== 'Not found') {
                                            $i++;
                                        }
                                    } elseif ($i >= $lead_distribution) {
                                        break;
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
