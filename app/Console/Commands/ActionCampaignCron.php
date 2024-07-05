<?php

namespace App\Console\Commands;

use App\Http\Controllers\LeadsController;
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
use Illuminate\Support\Facades\Auth;

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
                if ($current_time >= $action->ending_time) {
                    $campaign = Campaign::where('id', $action->campaign_id)->where('is_active', 1)->where('is_archive', 0)->first();
                    if (!empty($campaign)) {
                        if ($action->current_element_id == 'step_1') {
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
                                                $lc = new LeadsController();
                                                $lc->applySettings($campaign, $url);
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
                $this->error('Failed to insert data beacause ' . $e . ' at: ' . now());
            }
        }
    }
}
