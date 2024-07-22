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
        set_time_limit(300);
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
                $final_reactions = [];
                if (count($reactions) > 0) {
                    foreach ($reactions as $reaction) {
                        try {
                            $author = $reaction['author'];
                            $request = [
                                'account_id' => $account_id,
                                'profile_url' => $author['id'],
                                'sales_navigator' => true
                            ];
                            $profile = $uc->view_profile(new \Illuminate\Http\Request($request));
                            if ($profile instanceof JsonResponse) {
                                $profile = $profile->getData(true);
                                if (!isset($profile['error'])) {
                                    $profile = $profile['user_profile'];
                                    $conn = true;
                                    $connection_map = [
                                        1 => 'FIRST_DEGREE',
                                        2 => 'SECOND_DEGREE',
                                        3 => 'THIRD_DEGREE'
                                    ];
                                    if (
                                        isset($connection_map[$campaign['campaign_connection']]) &&
                                        $author['network_distance'] != $connection_map[$campaign['campaign_connection']]
                                    ) {
                                        $conn = false;
                                    }
                                    if ($conn) {
                                        $url = $profile['public_profile_url'];
                                        $lead = Leads::where('campaign_id', $campaign['id'])->where('profileUrl', $url)->first();
                                        $final_reactions[] = $reaction;
                                    }
                                } else {
                                    file_put_contents($logFilePath, 'Failed to insert data because ' . json_encode($profile['error']) . ' at: ' . now() . PHP_EOL, FILE_APPEND);
                                }
                            } else {
                                file_put_contents($logFilePath, 'Failed to insert data because User Profile is not instance of JsonResponse at: ' . now() . PHP_EOL, FILE_APPEND);
                            }
                        } catch (\Exception $e) {
                            file_put_contents($logFilePath, 'Failed to insert data because ' . $e->getMessage() . ' at: ' . now() . PHP_EOL, FILE_APPEND);
                        }
                    }
                    dd($final_reactions);
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
