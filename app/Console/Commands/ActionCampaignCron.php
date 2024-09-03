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
    protected $logFilePath;

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
        $this->logFilePath = storage_path('logs/campaign_action.log');
        file_put_contents($this->logFilePath, 'Action started succesfully at ' . now() . PHP_EOL, FILE_APPEND);
        file_put_contents($this->logFilePath, '{' . PHP_EOL, FILE_APPEND);
        try {
            $sc = new SeatController();
            $final_accounts = $sc->get_final_accounts();
            foreach ($final_accounts as $final_account) {
                $seats = SeatInfo::whereIn('account_id', $final_account)->get();
                $seat_ids = $seats->pluck('id')->toArray();
                $campaigns = Campaign::whereIn('seat_id', $seat_ids)->where('is_active', 1)->where('is_archive', 0)->get();
                if ($campaigns->isNotEmpty()) {
                    $this->campaign_working($campaigns);
                } else {
                    $fileContent = '    No campaigns found for seats # (' . implode(', ', $seats->pluck('id')->toArray()) . ')' . PHP_EOL;
                    file_put_contents($this->logFilePath, $fileContent, FILE_APPEND);
                }
            }
        } catch (Exception $e) {
            file_put_contents($this->logFilePath, $e . PHP_EOL, FILE_APPEND);
        }
        file_put_contents($this->logFilePath, '}' . PHP_EOL, FILE_APPEND);
    }

    private function campaign_working($campaigns, $remain_distribution_limit = 80, $campaignFunctionCount = 0)
    {
        file_put_contents($this->logFilePath, ' Working on campaigns ' . ++$campaignFunctionCount . ' times started succesfully at ' . now() . PHP_EOL, FILE_APPEND);
        file_put_contents($this->logFilePath, ' {' . PHP_EOL, FILE_APPEND);
        try {
            if ($remain_distribution_limit < count($campaigns)) {
                $campaigns = array_slice($campaigns, 0, $remain_distribution_limit);
            }
            $lead_distribution_limit = $remain_distribution_limit > 0 ? floor($remain_distribution_limit / count($campaigns)) : 0;
            $remain_distribution_limit = 0;
            $campaignsToRemove = [];
            foreach ($campaigns as $index => $campaign) {
                switch ($campaign['campaign_type']) {
                    case 'linkedin':
                        $remain_distribution_limit = $this->linkedinLeads($campaign, ($remain_distribution_limit + $lead_distribution_limit));
                        break;
                    case 'sales_navigator':
                        $remain_distribution_limit = $this->salesLeads($campaign, ($remain_distribution_limit + $lead_distribution_limit));
                        break;
                        //TODO: Recruiter Option
                    case 'import':
                        $remain_distribution_limit = $this->importLeads($campaign, ($remain_distribution_limit + $lead_distribution_limit));
                        break;
                    case 'post_engagement':
                        $remain_distribution_limit = $this->postLeads($campaign, ($remain_distribution_limit + $lead_distribution_limit));
                        break;
                    case 'leads_list':
                        $remain_distribution_limit = $this->leadList($campaign, ($remain_distribution_limit + $lead_distribution_limit));
                        break;
                    default:
                        file_put_contents($this->logFilePath, '     Unknown campaign type ' . $campaign['campaign_type'] . ' of campaign # ' . $campaign['id'] . PHP_EOL, FILE_APPEND);
                        break;
                }
                if ($remain_distribution_limit > 0) {
                    $campaignsToRemove[] = $index;
                }
            }
            foreach ($campaignsToRemove as $index) {
                unset($campaigns[$index]);
            }
            if ($campaigns->isNotEmpty() && $remain_distribution_limit > 0) {
                file_put_contents($this->logFilePath, ' }' . PHP_EOL, FILE_APPEND);
                $this->campaign_working($campaigns, $remain_distribution_limit, $campaignFunctionCount);
            } else {
                file_put_contents($this->logFilePath, ' }' . PHP_EOL, FILE_APPEND);
            }
        } catch (\Exception $e) {
            file_put_contents($this->logFilePath, $e . PHP_EOL, FILE_APPEND);
        }
    }

    private function linkedinLeads($campaign, $lead_distribution_limit, $leadCount = 0, $searchedLeadCount = 0)
    {
        file_put_contents($this->logFilePath, '     Searching Linkedin Leads started succesfully at ' . now() . PHP_EOL, FILE_APPEND);
        file_put_contents($this->logFilePath, '     {' . PHP_EOL, FILE_APPEND);
        try {
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
                'start' => $searchedLeadCount
            ];
            $uc = new UnipileController();
            $linkedin_search = $uc->linkedin_search(new \Illuminate\Http\Request($request));
            if (!isset($linkedin_search->getData(true)['error'])) {
                $searches = $linkedin_search->getData(true)['accounts'];
                if (count($searches) > 0) {
                    foreach ($searches as $search) {
                        $items = $search['items'];
                        foreach ($items as $item) {
                            if (isset($item['item']['entityResult'])) {
                                $result = $item['item']['entityResult'];
                                $k++;
                                try {
                                    if ($leadCount >= $lead_distribution_limit) {
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
                                    ];
                                    $profile = $uc->view_profile(new \Illuminate\Http\Request($request));
                                    if (!isset($profile->getData(true)['error'])) {
                                        $profile = $profile->getData(true)['user_profile'];
                                        if (strpos($profile['public_identifier'], 'https://www.linkedin.com/in/') !== false) {
                                            $url = $profile['public_identifier'];
                                        } else {
                                            $url = 'https://www.linkedin.com/in/' . $profile['public_identifier'];
                                        }
                                        $lead = Leads::where('campaign_id', $campaign['id'])->where('profileUrl', $url)->first();
                                        if (empty($lead) && $leadCount < $lead_distribution_limit) {
                                            $lc = new LeadsController();
                                            if ($lc->applySettings($campaign, $url)) {
                                                $leadCount++;
                                                file_put_contents($this->logFilePath, '         "' . $url . '" Lead inserted succesfully' . PHP_EOL, FILE_APPEND);
                                            }
                                        } else if (!empty($lead)) {
                                            file_put_contents($this->logFilePath, '         "' . $url . '" Lead already existed' . PHP_EOL, FILE_APPEND);
                                        }
                                    } else {
                                        file_put_contents($this->logFilePath, json_encode($profile->getData(true)['error']) . PHP_EOL, FILE_APPEND);
                                    }
                                } catch (\Exception $e) {
                                    file_put_contents($this->logFilePath, $e . PHP_EOL, FILE_APPEND);
                                }
                            }
                        }
                    }
                    if ($leadCount < $lead_distribution_limit) {
                        file_put_contents($this->logFilePath, '     }' . PHP_EOL, FILE_APPEND);
                        return $this->linkedinLeads($campaign, $lead_distribution_limit, $leadCount, ($k + $searchedLeadCount));
                    } else {
                        file_put_contents($this->logFilePath, '         Limitation reached' . PHP_EOL, FILE_APPEND);
                    }
                } else if ($leadCount < $lead_distribution_limit) {
                    file_put_contents($this->logFilePath, '         No more searches found' . PHP_EOL, FILE_APPEND);
                } else {
                    file_put_contents($this->logFilePath, '         Limitation reached' . PHP_EOL, FILE_APPEND);
                }
            } else {
                file_put_contents($this->logFilePath, json_encode($linkedin_search->getData(true)['error']) . PHP_EOL, FILE_APPEND);
            }
        } catch (Exception $e) {
            file_put_contents($this->logFilePath, $e . PHP_EOL, FILE_APPEND);
        }
        file_put_contents($this->logFilePath, '     }' . PHP_EOL, FILE_APPEND);
        return $lead_distribution_limit - $leadCount;
    }

    private function salesLeads($campaign, $lead_distribution_limit, $leadCount = 0, $searchedLeadCount = 0)
    {
        file_put_contents($this->logFilePath, '     Searching Sales Leads started succesfully at ' . now() . PHP_EOL, FILE_APPEND);
        file_put_contents($this->logFilePath, '     {' . PHP_EOL, FILE_APPEND);
        try {
            $seat = SeatInfo::where('id', $campaign['seat_id'])->first();
            $account_id = $seat['account_id'];
            $uc = new UnipileController();
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
                'start' => $searchedLeadCount
            ];
            $sales_navigator_search = $uc->sales_navigator_search(new \Illuminate\Http\Request($request));
            if (!isset($sales_navigator_search->getData(true)['error'])) {
                $searches = $sales_navigator_search->getData(true)['accounts'];
                if (count($searches) > 0) {
                    foreach ($searches as $search) {
                        try {
                            if ($leadCount >= $lead_distribution_limit) {
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
                            if (!isset($profile->getData(true)['error'])) {
                                $profile = $profile->getData(true)['user_profile'];
                                $url = $profile['public_profile_url'];
                                $lead = Leads::where('campaign_id', $campaign['id'])->where('profileUrl', $url)->first();
                                if (empty($lead) && $leadCount < $lead_distribution_limit) {
                                    $lc = new LeadsController();
                                    if ($lc->applySettings($campaign, $url)) {
                                        $leadCount++;
                                        file_put_contents($this->logFilePath, '         "' . $url . '" Lead inserted succesfully' . PHP_EOL, FILE_APPEND);
                                    }
                                } else if (!empty($lead)) {
                                    file_put_contents($this->logFilePath, '         "' . $url . '" Lead already existed' . PHP_EOL, FILE_APPEND);
                                }
                            } else {
                                file_put_contents($this->logFilePath, json_encode($profile->getData(true)['error']) . PHP_EOL, FILE_APPEND);
                            }
                        } catch (\Exception $e) {
                            file_put_contents($this->logFilePath, $e . PHP_EOL, FILE_APPEND);
                        }
                    }
                    if ($leadCount < $lead_distribution_limit) {
                        file_put_contents($this->logFilePath, '     }' . PHP_EOL, FILE_APPEND);
                        return $this->salesLeads($campaign, $lead_distribution_limit, $leadCount, (count($searches) + $searchedLeadCount));
                    } else {
                        file_put_contents($this->logFilePath, '         Limitation reached' . PHP_EOL, FILE_APPEND);
                    }
                } else if ($leadCount < $lead_distribution_limit) {
                    file_put_contents($this->logFilePath, '         No more searches found' . PHP_EOL, FILE_APPEND);
                } else {
                    file_put_contents($this->logFilePath, '         Limitation reached' . PHP_EOL, FILE_APPEND);
                }
            } else {
                file_put_contents($this->logFilePath, json_encode($sales_navigator_search->getData(true)['error']) . PHP_EOL, FILE_APPEND);
            }
        } catch (Exception $e) {
            file_put_contents($this->logFilePath, $e . PHP_EOL, FILE_APPEND);
        }
        file_put_contents($this->logFilePath, '     }' . PHP_EOL, FILE_APPEND);
        return $lead_distribution_limit - $leadCount;
    }

    private function importLeads($campaign, $lead_distribution_limit, $leadCount = 0)
    {
        file_put_contents($this->logFilePath, '     Searching Import Leads started succesfully at ' . now() . PHP_EOL, FILE_APPEND);
        file_put_contents($this->logFilePath, '     {' . PHP_EOL, FILE_APPEND);
        try {
            $imported_lead = ImportedLeads::where('user_id', $campaign['user_id'])->where('campaign_id', $campaign['id'])->first();
            $csvController = new CsvController();
            $csvData = $csvController->importedLeadToArray($imported_lead['file_path']);
            $have_url = false;
            if ($csvData !== NULL) {
                foreach ($csvData as $key => $value) {
                    if (str_contains(strtolower($key), 'url')) {
                        $have_url = true;
                        foreach ($value as $url) {
                            try {
                                if ($leadCount >= $lead_distribution_limit) {
                                    break 2;
                                }
                                $lead = Leads::where('campaign_id', $campaign['id'])->where('profileUrl', $url)->first();
                                if (empty($lead) && $leadCount < $lead_distribution_limit) {
                                    $lc = new LeadsController();
                                    if ($lc->applySettings($campaign, $url)) {
                                        $leadCount++;
                                        file_put_contents($this->logFilePath, '         "' . $url . '" Lead inserted succesfully' . PHP_EOL, FILE_APPEND);
                                    }
                                } else if (!empty($lead)) {
                                    file_put_contents($this->logFilePath, '         "' . $url . '" Lead already existed' . PHP_EOL, FILE_APPEND);
                                }
                            } catch (\Exception $e) {
                                file_put_contents($this->logFilePath, $e . PHP_EOL, FILE_APPEND);
                            }
                        }
                    }
                }
                if (!$have_url) {
                    file_put_contents($this->logFilePath, '         No URL column found' . PHP_EOL, FILE_APPEND);
                }
                if ($leadCount < $lead_distribution_limit) {
                    file_put_contents($this->logFilePath, '         No more searches found' . PHP_EOL, FILE_APPEND);
                } else {
                    file_put_contents($this->logFilePath, '         Limitation reached' . PHP_EOL, FILE_APPEND);
                }
            } else {
                file_put_contents($this->logFilePath, '         No data in csv file' . PHP_EOL, FILE_APPEND);
            }
        } catch (Exception $e) {
            file_put_contents($this->logFilePath, $e . PHP_EOL, FILE_APPEND);
        }
        file_put_contents($this->logFilePath, '     }' . PHP_EOL, FILE_APPEND);
        return $lead_distribution_limit - $leadCount;
    }

    private function postLeads($campaign, $lead_distribution_limit, $leadCount = 0, $searchedLeadCount = 0, $cursor = null, $is_comment = false)
    {
        file_put_contents($this->logFilePath, '     Searching Post Leads started succesfully at ' . now() . PHP_EOL, FILE_APPEND);
        file_put_contents($this->logFilePath, '     {' . PHP_EOL, FILE_APPEND);
        try {
            $seat = SeatInfo::where('id', $campaign->seat_id)->first();
            $account_id = $seat['account_id'];
            $matches = array();
            $url = $campaign['campaign_url'];
            preg_match('/activity-([0-9]+)/', $url, $matches);
            if (isset($matches[1])) {
                $request = [
                    'account_id' => $account_id,
                    'identifier' => $matches[1]
                ];
                $uc = new UnipileController();
                if (!isset($post_search)) {
                    $post_search = $uc->post_search(new \Illuminate\Http\Request($request));
                }
                $post = $post_search->getData(true)['post'];
                if (count($post) > 0) {
                    $request = [
                        'account_id' => $account_id,
                        'identifier' => $post['social_id'],
                        'cursor' => $cursor
                    ];
                    if ($is_comment) {
                        $response_post_search = $uc->comments_post_search(new \Illuminate\Http\Request($request));
                        if (count($response_post_search->getData(true)['reactions']['items']) > 0) {
                            $paging['cursor'] = $response_post_search->getData(true)['reactions']['cursor'];
                        } else {
                            $paging['cursor'] = null;
                        }
                        $reactions = $response_post_search->getData(true)['reactions']['items'];
                    } else {
                        $response_post_search = $uc->reactions_post_search(new \Illuminate\Http\Request($request));
                        $paging = $response_post_search->getData(true)['reactions']['paging'];
                        $reactions = $response_post_search->getData(true)['reactions']['items'];
                    }
                    if (count($reactions) > 0) {
                        foreach ($reactions as $reaction) {
                            try {
                                if ($leadCount >= $lead_distribution_limit) {
                                    break;
                                }
                                if ($is_comment) {
                                    $author = $reaction['author_details'];
                                } else {
                                    $author = $reaction['author'];
                                }
                                $request = [
                                    'account_id' => $account_id,
                                    'profile_url' => $author['id'],
                                ];
                                $profile = $uc->view_profile(new \Illuminate\Http\Request($request));
                                if (!isset($profile->getData(true)['error'])) {
                                    $profile = $profile->getData(true)['user_profile'];
                                    $conn = true;
                                    $connection_map = [
                                        1 => ['DISTANCE_1', 'FIRST_DEGREE'],
                                        2 => ['DISTANCE_2', 'SECOND_DEGREE'],
                                        3 => ['DISTANCE_3', 'THIRD_DEGREE']
                                    ];
                                    if (
                                        isset($connection_map[$campaign['campaign_connection']]) &&
                                        !in_array($author['network_distance'], $connection_map[$campaign['campaign_connection']])
                                    ) {
                                        $conn = false;
                                    }
                                    if ($conn) {
                                        if (strpos($profile['public_identifier'], 'https://www.linkedin.com/in/') !== false) {
                                            $url = $profile['public_identifier'];
                                        } else {
                                            $url = 'https://www.linkedin.com/in/' . $profile['public_identifier'];
                                        }
                                        $lead = Leads::where('campaign_id', $campaign['id'])->where('profileUrl', $url)->first();
                                        if (empty($lead) && $leadCount < $lead_distribution_limit) {
                                            $lc = new LeadsController();
                                            if ($lc->applySettings($campaign, $url)) {
                                                $leadCount++;
                                                file_put_contents($this->logFilePath, '         "' . $url . '" Lead inserted succesfully' . PHP_EOL, FILE_APPEND);
                                            }
                                        } else if (!empty($lead)) {
                                            file_put_contents($this->logFilePath, '         "' . $url . '" Lead already existed' . PHP_EOL, FILE_APPEND);
                                        }
                                    } else {
                                        file_put_contents($this->logFilePath, '         "' . $url . '" Connection is not ' . $campaign['campaign_connection'] . PHP_EOL, FILE_APPEND);
                                    }
                                } else {
                                    file_put_contents($this->logFilePath, json_encode($profile->getData(true)['error']) . PHP_EOL, FILE_APPEND);
                                }
                            } catch (\Exception $e) {
                                file_put_contents($this->logFilePath, $e . PHP_EOL, FILE_APPEND);
                            }
                        }
                    }
                    if ($leadCount < $lead_distribution_limit) {
                        if (isset($paging['cursor'])) {
                            if (!$is_comment) {
                                file_put_contents($this->logFilePath, '     }' . PHP_EOL, FILE_APPEND);
                                return $this->postLeads($campaign, $lead_distribution_limit, $leadCount, (count($reactions) + $searchedLeadCount), $paging['cursor'], false, $post_search);
                            } else if ($is_comment) {
                                file_put_contents($this->logFilePath, '     }' . PHP_EOL, FILE_APPEND);
                                return $this->postLeads($campaign, $lead_distribution_limit, $leadCount, (count($reactions) + $searchedLeadCount), $paging['cursor'], true, $post_search);
                            }
                        } else if (!isset($paging['cursor'])) {
                            if (!$is_comment) {
                                file_put_contents($this->logFilePath, '     }' . PHP_EOL, FILE_APPEND);
                                return $this->postLeads($campaign, $lead_distribution_limit, $leadCount, (count($reactions) + $searchedLeadCount), null, true, $post_search);
                            }
                        } else {
                            file_put_contents($this->logFilePath, '         No more searches found' . PHP_EOL, FILE_APPEND);
                        }
                    } else {
                        file_put_contents($this->logFilePath, '         Limitation reached' . PHP_EOL, FILE_APPEND);
                    }
                } else if ($leadCount < $lead_distribution_limit) {
                    file_put_contents($this->logFilePath, '         No more searches found' . PHP_EOL, FILE_APPEND);
                } else {
                    file_put_contents($this->logFilePath, '         Limitation reached' . PHP_EOL, FILE_APPEND);
                }
            } else if ($leadCount < $lead_distribution_limit) {
                file_put_contents($this->logFilePath, '         No more searches found' . PHP_EOL, FILE_APPEND);
            } else {
                file_put_contents($this->logFilePath, '         Limitation reached' . PHP_EOL, FILE_APPEND);
            }
        } catch (Exception $e) {
            file_put_contents($this->logFilePath, $e . PHP_EOL, FILE_APPEND);
        }
        file_put_contents($this->logFilePath, '     }' . PHP_EOL, FILE_APPEND);
        return $lead_distribution_limit - $leadCount;
    }

    private function leadList($campaign, $lead_distribution_limit, $leadCount = 0, $cursor = null)
    {
        file_put_contents($this->logFilePath, '     Searching Post Leads started succesfully at ' . now() . PHP_EOL, FILE_APPEND);
        file_put_contents($this->logFilePath, '     {' . PHP_EOL, FILE_APPEND);
        try {
            $seat = SeatInfo::where('id', $campaign->seat_id)->first();
            $account_id = $seat['account_id'];
            $url = $campaign['campaign_url'];
            $request = [
                'account_id' => $account_id,
                'search_url' => $url,
                'cursor' => $cursor
            ];
            $uc = new UnipileController();
            $lead_list_search = $uc->lead_list_search(new \Illuminate\Http\Request($request));
            $searches = $lead_list_search->getData(true)['accounts']['items'];
            $cursor = $lead_list_search->getData(true)['accounts']['cursor'];
            if (count($searches) > 0) {
                foreach ($searches as $search) {
                    if ($leadCount >= $lead_distribution_limit) {
                        break;
                    }
                    $request = [
                        'account_id' => $account_id,
                        'profile_url' => $search['public_profile_url'],
                    ];
                    $profile = $uc->view_profile(new \Illuminate\Http\Request($request));
                    if (!isset($profile->getData(true)['error'])) {
                        $profile = $profile->getData(true)['user_profile'];
                        if (strpos($profile['public_identifier'], 'https://www.linkedin.com/in/') !== false) {
                            $url = $profile['public_identifier'];
                        } else {
                            $url = 'https://www.linkedin.com/in/' . $profile['public_identifier'];
                        }
                        $lead = Leads::where('campaign_id', $campaign['id'])->where('profileUrl', $url)->first();
                        if (empty($lead) && $leadCount < $lead_distribution_limit) {
                            $lc = new LeadsController();
                            if ($lc->applySettings($campaign, $url)) {
                                $leadCount++;
                                file_put_contents($this->logFilePath, '         "' . $url . '" Lead inserted succesfully' . PHP_EOL, FILE_APPEND);
                            }
                        } else if (!empty($lead)) {
                            file_put_contents($this->logFilePath, '         "' . $url . '" Lead already existed' . PHP_EOL, FILE_APPEND);
                        }
                    }
                }
                if ($leadCount < $lead_distribution_limit && !is_null($cursor)) {
                    file_put_contents($this->logFilePath, '     }' . PHP_EOL, FILE_APPEND);
                    return $this->addLeadList($campaign, $lead_distribution_limit, $leadCount, $cursor);
                } else {
                    file_put_contents($this->logFilePath, '         Limitation reached' . PHP_EOL, FILE_APPEND);
                }
            } else if ($leadCount < $lead_distribution_limit) {
                file_put_contents($this->logFilePath, '         No more searches found' . PHP_EOL, FILE_APPEND);
            } else {
                file_put_contents($this->logFilePath, '         Limitation reached' . PHP_EOL, FILE_APPEND);
            }
        } catch (Exception $e) {
            file_put_contents($this->logFilePath, $e . PHP_EOL, FILE_APPEND);
        }
        file_put_contents($this->logFilePath, '     }' . PHP_EOL, FILE_APPEND);
        return $lead_distribution_limit - $leadCount;
    }
}
