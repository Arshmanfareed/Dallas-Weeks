<?php

namespace App\Console\Commands;

use App\Http\Controllers\LinkedinSettingController;
use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Models\CampaignActions;
use App\Models\CampaignElement;
use App\Models\CampaignPath;
use App\Models\ElementProperties;
use Illuminate\Http\Request;
use App\Models\ImportedLeads;
use App\Models\LeadActions;
use App\Models\Leads;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;
use App\Models\SeatInfo;
use App\Models\UpdatedCampaignElements;
use App\Models\UpdatedCampaignProperties;
use App\Http\Controllers\UnipileController;

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
        $actions = CampaignActions::where('status', 'inprogress')->get();
        $current_time = now();
        foreach ($actions as $action) {
            try {
                $lsc = new LinkedinSettingController();
                if ($current_time >= $action->ending_time) {
                    $campaign = Campaign::where('id', $action->campaign_id)->where('is_active', 1)->where('is_archive', 0)->first();
                    if (!empty($campaign)) {
                        if ($action->current_element_id == 'step_1') {
                            $seat = SeatInfo::where('id', $campaign->seat_id)->first();
                            $account_id = $seat['account_id'];
                            if ($campaign->campaign_type == 'import') {
                                $imported_leads = ImportedLeads::where('user_id', $campaign->user_id)->where('campaign_id', $campaign->id)->first();
                                $fileHandle = fopen(storage_path('app/uploads/' . $imported_leads->file_path), 'r');
                                if ($fileHandle !== false) {
                                    $csvData = [];
                                    $delimiter = ',';
                                    $enclosure = '"';
                                    $escape = '\\';
                                    $columnNames = fgetcsv($fileHandle, 0, $delimiter, $enclosure, $escape);
                                    foreach ($columnNames as $colName) {
                                        $csvData[$colName] = [];
                                    }
                                    while (($rowData = fgetcsv($fileHandle, 0, $delimiter, $enclosure, $escape)) !== false) {
                                        foreach ($columnNames as $index => $colName) {
                                            $csvData[$colName][] = $rowData[$index] ?? null;
                                        }
                                    }
                                    foreach ($csvData as $key => $value) {
                                        foreach ($value as $url) {
                                            if (str_contains(strtolower($key), 'url')) {
                                                $uc = new UnipileController();
                                                $profile = [
                                                    'account_id' => $account_id,
                                                    'profile_url' => $url,
                                                ];
                                                $user_profile = $uc->view_profile(new \Illuminate\Http\Request($profile));
                                                if ($user_profile instanceof JsonResponse) {
                                                    $user_profile = $user_profile->getData(true);
                                                    $user_profile = $user_profile['user_profile'];
                                                    if (!isset($user_profile['error'])) {
                                                        if (($lsc->get_value_of_setting($campaign->id, 'linkedin_settings_discover_premium_linked_accounts_only') && $user_profile['is_premium'])
                                                            || (!$lsc->get_value_of_setting($campaign->id, 'linkedin_settings_discover_premium_linked_accounts_only') && !$user_profile['is_premium'])
                                                        ) {
                                                            $lead = new Leads();
                                                            $lead->is_active = 1;
                                                            $lead->contact = '';
                                                            $lead->title_company = '';
                                                            $lead->send_connections = 'discovered';
                                                            $lead->next_step = '';
                                                            $lead->executed_time = date('H:i:s');
                                                            $lead->campaign_id = $campaign->id;
                                                            $lead->user_id = $campaign->user_id;
                                                            $lead->created_at = now();
                                                            $lead->updated_at = now();
                                                            $lead->profileUrl = $url;
                                                            if (isset($user_profile['first_name']) && isset($user_profile['last_name'])) {
                                                                $name = $user_profile['first_name'] . ' ' . $user_profile['last_name'];
                                                                $name = ucwords($name);
                                                                $lead->title_company = $name;
                                                            }
                                                            if (isset($user_profile['name'])) {
                                                                $name = $user_profile['name'];
                                                                $lead->title_company = $name;
                                                            }
                                                            if ($lsc->get_value_of_setting($campaign->id, 'linkedin_settings_collect_contact_information')) {
                                                                if (isset($user_profile['contact_info']['phones'])) {
                                                                    $contact = $user_profile['contact_info']['phones'][0];
                                                                    $lead->contact = $contact;
                                                                }
                                                                if (isset($user_profile['phone'])) {
                                                                    $contact = $user_profile['phone'];
                                                                    $lead->contact = $contact;
                                                                }
                                                                if (isset($user_profile['contact_info']['emails'])) {
                                                                    $email = $user_profile['contact_info']['emails'][0];
                                                                    $lead->email = $email;
                                                                }
                                                            }
                                                            $lead->save();
                                                            if (isset($lead->id)) {
                                                                $lead_action = new LeadActions();
                                                                $campaign_path = CampaignPath::where('campaign_id', $campaign->id)->orderBy('id')->first();
                                                                $lead_action->current_element_id = 'step_1';
                                                                $lead_action->next_true_element_id = $campaign_path->current_element_id;
                                                                $lead_action->campaign_id = $campaign->id;
                                                                $lead_action->next_false_element_id = '';
                                                                $lead_action->created_at = now();
                                                                $lead_action->updated_at = now();
                                                                $lead_action->status = 'inprogress';
                                                                $lead_action->lead_id = $lead->id;
                                                                $lead_action->ending_time = now();
                                                                $lead_action->save();
                                                            }
                                                        }
                                                    } else {
                                                        $this->error($user_profile['error']);
                                                    }
                                                } else {
                                                    $this->error('User Profile not Json Response');
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $action->status = 'completed';
                        $action->save();
                        if ($action->next_true_element_id != '' || $action->next_false_element_id != '') {
                            $campaign_path = CampaignPath::where('current_element_id', $action->next_true_element_id)->first();
                            $new_action = new CampaignActions();
                            $new_action->current_element_id = $action->next_true_element_id;
                            if (!empty($campaign_path)) {
                                $new_action->next_true_element_id = $campaign_path->next_true_element_id;
                                $new_action->next_false_element_id = $campaign_path->next_false_element_id;
                            } else {
                                $new_action->next_true_element_id = '';
                                $new_action->next_false_element_id = '';
                            }
                            $new_action->created_at = now();
                            $new_action->updated_at = now();
                            $new_action->campaign_id = $campaign->id;
                            $new_action->status = 'inprogress';
                            $properties = UpdatedCampaignProperties::where('element_id', $new_action->current_element_id)->get();
                            $time = now();
                            foreach ($properties as $property) {
                                $campaign_property = ElementProperties::where('id', $property->property_id)->first();
                                if (!empty($campaign_property) && isset($property->value)) {
                                    $timeToAdd = intval($property->value);
                                    if ($campaign_property->property_name == 'Hours') {
                                        $time->modify('+' . $timeToAdd . ' hours');
                                    } else if ($campaign_property->property_name == 'Days') {
                                        $time->modify('+' . $timeToAdd . ' days');
                                    }
                                }
                            }
                            $new_action->ending_time = $time->format('Y-m-d H:i:s');
                            $new_action->save();
                        }
                        $this->info('Data inserted successfully.' . now());
                    } else {
                        $this->error('No Campaign Found of Id: ' . $action->campaign_id . ' at: ' . now());
                    }
                } else {
                    $this->error('Campaign Action to be update is not arrived at: ' . now());
                }
            } catch (\Exception $e) {
                $this->error('Failed to insert data: ' . $e->getMessage() . ' at: ' . now());
            }
        }
    }
}
