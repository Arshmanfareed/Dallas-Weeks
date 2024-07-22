<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\SeatInfo;
use GuzzleHttp\Client;
use App\Models\LeadActions;
use App\Models\CampaignElement;
use App\Models\CampaignPath;
use App\Models\ElementProperties;
use App\Http\Controllers\CronController;
use App\Models\Leads;
use App\Models\ImportedLeads;
use App\Models\UpdatedCampaignElements;
use App\Models\UpdatedCampaignProperties;
use Illuminate\Http\JsonResponse;

use function PHPUnit\Framework\isEmpty;

class TestController extends Controller
{
    public function base()
    {
        $campaign = Campaign::where('campaign_name', 'New Test Post')->where('campaign_type', 'post_engagement')->first();
        $logFilePath = storage_path('logs/campaign_action.log');
        $seat = SeatInfo::where('id', $campaign->seat_id)->first();
        $account_id = $seat['account_id'];
        $url = $campaign['campaign_url'];
        preg_match('/activity-([0-9]+)/', $url, $matches);
        if (isset($matches[1])) {
            $request = [
                'account_id' => $account_id,
                'identifier' => $matches[1]
            ];
            $uc = new UnipileController();
            $post = $uc->post_search(new \Illuminate\Http\Request($request));
            $post = $post->getData(true)['post'];
            if (count($post) > 0) {
                $request = [
                    'account_id' => $account_id,
                    'identifier' => $post['social_id'],
                ];
                $reactions = $uc->reactions_post_search(new \Illuminate\Http\Request($request));
                $paging = $reactions->getData(true)['reactions']['paging'];
                $reactions = $reactions->getData(true)['reactions']['items'];
                if (count($reactions) > 0) {
                    dd($reactions);
                } else {
                    file_put_contents($logFilePath, 'Failed to insert data because No more searches found at: ' . now() . PHP_EOL, FILE_APPEND);
                }
            } else {
                file_put_contents($logFilePath, 'Failed to insert data because No post found at: ' . now() . PHP_EOL, FILE_APPEND);
            }
        } else {
            file_put_contents($logFilePath, 'Failed to insert data because No activity keyward found in post URL at: ' . now() . PHP_EOL, FILE_APPEND);
        }
    }
}
