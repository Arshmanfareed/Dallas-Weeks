<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\SeatInfo;
use App\Models\LeadActions;
use App\Models\CampaignElement;
use App\Models\CampaignPath;
use App\Models\ElementProperties;
use App\Http\Controllers\CronController;
use App\Models\Leads;
use App\Models\ImportedLeads;
use App\Models\UpdatedCampaignElements;
use App\Models\UpdatedCampaignProperties;

use function PHPUnit\Framework\isEmpty;

class TestController extends Controller
{
    public function base()
    {
        $logFilePath = storage_path('logs/lead_action.log');
        try {
            $current_time = now();
            $sc = new SeatController();
            $final_accounts = $sc->get_final_accounts();
            foreach ($final_accounts as $final_account) {
                $seats = SeatInfo::whereIn('account_id', $final_account)->get();
                $campaigns = Campaign::whereIn('seat_id', $seats->pluck('id')->toArray())->where('is_active', 1)->where('is_archive', 0)->get();
                if (count($campaigns) > 0) {
                    $lc = new LeadsController();
                    $view_distribution_limit = $lc->get_view_count($campaigns);
                    $invitation_distribution_limit = $lc->get_invite_count($campaigns);
                    $message_distribution_limit = $lc->get_message_count($campaigns);
                    echo '<pre>';
                    echo $view_distribution_limit;
                    echo '<pre>';
                    echo $invitation_distribution_limit;
                    echo '<pre>';
                    echo $message_distribution_limit;
                } else {
                    file_put_contents($logFilePath, 'Failed to insert data because No campaign found at: ' . now() . PHP_EOL, FILE_APPEND);
                }
            }
        } catch (\Exception $e) {
            file_put_contents($logFilePath, 'Failed to insert data because ' . $e->getMessage() . ' at: ' . now() . PHP_EOL, FILE_APPEND);
        }
    }
}
