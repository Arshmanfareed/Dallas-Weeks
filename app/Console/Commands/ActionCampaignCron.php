<?php

namespace App\Console\Commands;

use App\Http\Controllers\LeadsController;
use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Models\ImportedLeads;
use App\Models\Leads;
use App\Models\SeatInfo;
use App\Http\Controllers\UnipileController;
use App\Http\Controllers\CsvController;
use App\Http\Controllers\SeatController;
use Illuminate\Http\JsonResponse;
use Exception;

class ActionCampaignCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'action:campaign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for campaign actions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $logFilePath = storage_path('logs/campaign_action.log');
        file_put_contents($logFilePath, 'Action Campaigns started at: ' . now() . PHP_EOL, FILE_APPEND);
        try {
            $sc = new SeatController();
            $final_accounts = $sc->get_final_accounts();
            foreach ($final_accounts as $final_account) {
                $seats = SeatInfo::whereIn('account_id', $final_account)->get();
                $campaigns = Campaign::whereIn('seat_id', $seats->pluck('id')->toArray())->where('is_active', 1)->where('is_archive', 0)->get();
                if (count($campaigns) > 0) {
                    $lead_distribution_limit = floor(80 / count($campaigns));
                    foreach ($campaigns as $campaign) {
                        if ($campaign['campaign_type'] == 'import') {
                            $this->addImportLeads($campaign, $lead_distribution_limit);
                        } else if ($campaign['campaign_type'] == 'sales_navigator') {
                            $this->addSalesLeads($campaign, $lead_distribution_limit, 0, 0);
                        } else if ($campaign['campaign_type'] == 'linkedin') {
                            $this->addLinkedinLeads($campaign, $lead_distribution_limit, 0, 0);
                        } else if ($campaign['campaign_type'] == 'post_engagement') {
                            $this->addPostLeads($campaign, $lead_distribution_limit, 0, 0);
                        }
                    }
                } else {
                    file_put_contents($logFilePath, 'Failed to insert data because No campaign found at: ' . now() . PHP_EOL, FILE_APPEND);
                }
            }
        } catch (\Exception $e) {
            file_put_contents($logFilePath, 'Failed to insert data because ' . $e->getMessage() . ' at: ' . now() . PHP_EOL, FILE_APPEND);
        }
    }

    private function addImportLeads($campaign, $lead_distribution_limit)
    {
        $logFilePath = storage_path('logs/campaign_action.log');
        $imported_lead = ImportedLeads::where('user_id', $campaign['user_id'])->where('campaign_id', $campaign['id'])->first();
        $csvController = new CsvController();
        $csvData = $csvController->importedLeadToArray($imported_lead['file_path']);
        $i = 0;
        if ($csvData !== NULL) {
            foreach ($csvData as $key => $value) {
                if (str_contains(strtolower($key), 'url')) {
                    foreach ($value as $url) {
                        try {
                            if ($i >= $lead_distribution_limit) {
                                break;
                            }
                            $lead = Leads::where('campaign_id', $campaign['id'])->where('profileUrl', $url)->first();
                            if (empty($lead) && $i < $lead_distribution_limit) {
                                $lc = new LeadsController();
                                if ($lc->applySettings($campaign, $url) !== 'Not found') {
                                    $i++;
                                    file_put_contents($logFilePath, 'Lead inserted succesfully at: ' . now() . PHP_EOL, FILE_APPEND);
                                }
                            } else if (!empty($lead)) {
                                file_put_contents($logFilePath, 'Failed to insert data because Lead already existed at: ' . now() . PHP_EOL, FILE_APPEND);
                            }
                        } catch (\Exception $e) {
                            file_put_contents($logFilePath, 'Failed to insert data because ' . $e->getMessage() . ' at: ' . now() . PHP_EOL, FILE_APPEND);
                        }
                    }
                } else {
                    file_put_contents($logFilePath, 'Failed to insert data because No URL column found at: ' . now() . PHP_EOL, FILE_APPEND);
                }
            }
            file_put_contents($logFilePath, 'Failed to insert data because No more searches found at: ' . now() . PHP_EOL, FILE_APPEND);
        } else {
            file_put_contents($logFilePath, 'Failed to insert data because No data in csv file at: ' . now() . PHP_EOL, FILE_APPEND);
        }
    }

    private function addSalesLeads($campaign, $lead_distribution_limit, $i, $j)
    {
        $logFilePath = storage_path('logs/campaign_action.log');
        $seat = SeatInfo::where('id', $campaign->seat_id)->first();
        $account_id = $seat['account_id'];
        $query = '';
        $url = $campaign['campaign_url'];
        $parsed_url = parse_url($url);
        $query_string = isset($parsed_url['query']) ? $parsed_url['query'] : '';
        parse_str($query_string, $params);
        $query = isset($params['query']) ? $params['query'] : null;
        $request = [
            'account_id' => $account_id,
            'query' => $query,
            'count' => 80,
            'start' => $j
        ];
        $uc = new UnipileController();
        $searches = $uc->sales_navigator_search(new \Illuminate\Http\Request($request));
        $searches = $searches->getData(true)['accounts'];
        if (count($searches) > 0) {
            foreach ($searches as $search) {
                try {
                    if ($i >= $lead_distribution_limit) {
                        break;
                    }
                    $profileUrl = str_replace('urn:li:fs_salesProfile:(', '', $search['entityUrn']);
                    $index = strpos($profileUrl, ',');
                    if ($index !== false) {
                        $profileUrl = substr($profileUrl, 0, $index);
                    }
                    $request = [
                        'account_id' => $account_id,
                        'profile_url' => $profileUrl,
                        'sales_navigator' => true,
                    ];
                    $profile = $uc->view_profile(new \Illuminate\Http\Request($request));
                    if ($profile instanceof JsonResponse) {
                        $profile = $profile->getData(true);
                        if (!isset($profile['error'])) {
                            $profile = $profile['user_profile'];
                            $url = $profile['public_profile_url'];
                            $lead = Leads::where('campaign_id', $campaign['id'])->where('profileUrl', $url)->first();
                            if (empty($lead) && $i < $lead_distribution_limit) {
                                $lc = new LeadsController();
                                if ($lc->applySettings($campaign, $url) !== 'Not found') {
                                    $i++;
                                    file_put_contents($logFilePath, 'Lead inserted succesfully at: ' . now() . PHP_EOL, FILE_APPEND);
                                }
                            } else if (!empty($lead)) {
                                file_put_contents($logFilePath, 'Failed to insert data because Lead already existed at: ' . now() . PHP_EOL, FILE_APPEND);
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
            if ($i + 1 < $lead_distribution_limit) {
                $this->addSalesLeads($campaign, $lead_distribution_limit, $i, count($searches) + $j);
            }
        } else {
            file_put_contents($logFilePath, 'Failed to insert data because No more searches found at: ' . now() . PHP_EOL, FILE_APPEND);
        }
    }

    private function addLinkedinLeads($campaign, $lead_distribution_limit, $i, $j)
    {
        $logFilePath = storage_path('logs/campaign_action.log');
        $seat = SeatInfo::where('id', $campaign->seat_id)->first();
        $account_id = $seat['account_id'];
        $query = '';
        $url = $campaign['campaign_url'];
        $queryString = parse_url($url, PHP_URL_QUERY);
        parse_str($queryString, $params);
        $query = [];
        $k = 0;
        foreach ($params as $key => $value) {
            $decodedValue = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if ($key === 'keywords' && is_string($decodedValue)) {
                    $query[$key] = rawurlencode(trim($decodedValue));
                } else {
                    $query[$key] = $decodedValue;
                }
            } else {
                $query[$key] = rawurlencode(trim($value));
            }
        }
        $request = [
            'account_id' => $account_id,
            'query' => $query,
            'start' => $j
        ];
        $uc = new UnipileController();
        $searches = $uc->linkedin_search(new \Illuminate\Http\Request($request));
        $searches = $searches->getData(true)['accounts'];
        if (count($searches) > 0) {
            foreach ($searches as $search) {
                $items = $search['items'];
                foreach ($items as $item) {
                    if (isset($item['item']['entityResult'])) {
                        $result = $item['item']['entityResult'];
                        $k++;
                        try {
                            if ($i >= $lead_distribution_limit) {
                                break;
                            }
                            $profileUrl = str_replace('urn:li:fsd_entityResultViewModel:(urn:li:fsd_profile:', '', $result['entityUrn']);
                            $index = strpos($profileUrl, ',');
                            if ($index !== false) {
                                $profileUrl = substr($profileUrl, 0, $index);
                            }
                            $request = [
                                'account_id' => $account_id,
                                'profile_url' => $profileUrl,
                                'sales_navigator' => true,
                            ];
                            $profile = $uc->view_profile(new \Illuminate\Http\Request($request));
                            if ($profile instanceof JsonResponse) {
                                $profile = $profile->getData(true);
                                if (!isset($profile['error'])) {
                                    $profile = $profile['user_profile'];
                                    $url = $profile['public_profile_url'];
                                    $lead = Leads::where('campaign_id', $campaign['id'])->where('profileUrl', $url)->first();
                                    if (empty($lead) && $i < $lead_distribution_limit) {
                                        $lc = new LeadsController();
                                        if ($lc->applySettings($campaign, $url) !== 'Not found') {
                                            $i++;
                                            file_put_contents($logFilePath, 'Lead inserted succesfully at: ' . now() . PHP_EOL, FILE_APPEND);
                                        }
                                    } else if (!empty($lead)) {
                                        file_put_contents($logFilePath, 'Failed to insert data because Lead already existed at: ' . now() . PHP_EOL, FILE_APPEND);
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
                }
            }
            if ($i + 1 < $lead_distribution_limit) {
                $this->addLinkedinLeads($campaign, $lead_distribution_limit, $i, $k + $j);
            }
        } else {
            file_put_contents($logFilePath, 'Failed to insert data because No more searches found at: ' . now() . PHP_EOL, FILE_APPEND);
        }
    }

    private function addPostLeads($campaign, $lead_distribution_limit, $i, $j, $cursor = null)
    {
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
                    'cursor' => $cursor
                ];
                $reactions = $uc->reactions_post_search(new \Illuminate\Http\Request($request));
                $paging = $reactions->getData(true)['reactions']['paging'];
                $reactions = $reactions->getData(true)['reactions']['items'];
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
                                    $url = $profile['public_profile_url'];
                                    $lead = Leads::where('campaign_id', $campaign['id'])->where('profileUrl', $url)->first();
                                    if (empty($lead) && $i < $lead_distribution_limit) {
                                        $lc = new LeadsController();
                                        if ($lc->applySettings($campaign, $url) !== 'Not found') {
                                            $i++;
                                            file_put_contents($logFilePath, 'Lead inserted succesfully at: ' . now() . PHP_EOL, FILE_APPEND);
                                        }
                                    } else if (!empty($lead)) {
                                        file_put_contents($logFilePath, 'Failed to insert data because Lead already existed at: ' . now() . PHP_EOL, FILE_APPEND);
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
                } else {
                    file_put_contents($logFilePath, 'Failed to insert data because No more searches found at: ' . now() . PHP_EOL, FILE_APPEND);
                }
                if ($i + 1 < $lead_distribution_limit && isset($paging['cursor'])) {
                    $this->addPostLeads($campaign, $lead_distribution_limit, $i, count($reactions) + $j, $paging['cursor']);
                } else {
                    if (!isset($paging['cursor'])) {
                        file_put_contents($logFilePath, 'Failed to insert data because No more searches found at: ' . now() . PHP_EOL, FILE_APPEND);
                    }
                }
            } else {
                file_put_contents($logFilePath, 'Failed to insert data because No post found at: ' . now() . PHP_EOL, FILE_APPEND);
            }
        } else {
            file_put_contents($logFilePath, 'Failed to insert data because No activity keyward found in post URL at: ' . now() . PHP_EOL, FILE_APPEND);
        }
    }
}
