<?php

namespace App\Console\Commands;

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
use App\Http\Controllers\CronController;
use App\Models\SeatInfo;
use App\Models\UpdatedCampaignElements;
use App\Models\UpdatedCampaignProperties;

class ActionLeadCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'action:lead';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for lead actions';

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
        $lead_actions = LeadActions::where('status', 'inprogress')->get();
        $current_time = now();
        foreach ($lead_actions as $action) {
            try {
                $campaign = Campaign::where('id', $action->campaign_id)->where('is_active', 1)->where('is_archive', 0)->first();
                if (!empty($campaign)) {
                    $success = false;
                    $conditional_output = '';
                    if ($action->current_element_id != 'step_1') {
                        $campaign_element = UpdatedCampaignElements::where('id', $action->current_element_id)->first();
                        $element = CampaignElement::where('id', $campaign_element->element_id)->first();
                        $seat = SeatInfo::where('id', $campaign->seat_id)->first();
                        $account_id = $seat['account_id'];
                        if ($current_time <= $action->ending_time) {
                            $cc = new CronController();
                            if ($element->element_slug == 'view_profile') {
                                $success = $cc->view_profile($action, $account_id);
                                if ($success) {
                                    $this->info('Profile viewed successfully at: ' . now());
                                }
                            } else if ($element->element_slug == 'invite_to_connect') {
                                $success = $cc->invite_to_connect($action, $account_id, $element, $campaign_element);
                                if ($success) {
                                    $this->info('Invitation to connect sent successfully at: ' . now());
                                }
                            } else if ($element->element_slug == 'message') {
                                $success = $cc->message($action, $account_id, $element, $campaign_element);
                                if ($success) {
                                    $this->info('Message sent successfully at: ' . now());
                                }
                            } else if ($element->element_slug == 'inmail_message') {
                                $success = $cc->inmail_message($action, $account_id, $element, $campaign_element);
                                if ($success) {
                                    $this->info('Inmail message sent successfully at: ' . now());
                                }
                            } else if ($element->element_slug == 'follow') {
                                $success = $cc->follow($action, $account_id);
                                if ($success) {
                                    $this->info('Follow user successfully at: ' . now());
                                }
                            } else if ($element->element_slug == 'email_message') {
                                $success = $cc->email_message($action, $account_id, $element, $campaign_element);
                                if ($success) {
                                    $this->info('Email sent successfully at: ' . now());
                                }
                            } else if ($element->element_slug == 'if_connected') {
                                $conditional_output = $cc->if_connected($action, $account_id);
                                if ($conditional_output == 'true') {
                                    $this->info('Lead is already connected at: ' . now());
                                } else {
                                    $this->info('Lead is not connected at: ' . now());
                                }
                                $success = true;
                            } else if ($element->element_slug == 'if_email_is_opened') {
                                $conditional_output = $cc->if_email_is_opened($action);
                                if ($conditional_output == 'true') {
                                    $this->info('Email is already opened at: ' . now());
                                } else {
                                    $this->info('Email is not opened at: ' . now());
                                }
                                $success = true;
                            } else if ($element->element_slug == 'if_has_imported_email') {
                                $conditional_output = $cc->if_has_imported_email($action);
                                if ($conditional_output == 'true') {
                                    $this->info('Email is already opened at: ' . now());
                                } else {
                                    $this->info('Email is not opened at: ' . now());
                                }
                                $success = true;
                            } else if ($element->element_slug == 'if_has_verified_email') {
                                $conditional_output = $cc->if_has_verified_email($action);
                                if ($conditional_output == 'true') {
                                    $this->info('Email is already verified at: ' . now());
                                } else {
                                    $this->info('Email is not verified at: ' . now());
                                }
                                $success = true;
                            } else if ($element->element_slug == 'if_free_inmail') {
                                $conditional_output = $cc->if_free_inmail($action);
                                if ($conditional_output == 'true') {
                                    $this->info('Inmail is already free at: ' . now());
                                } else {
                                    $this->info('Inmail is not free at: ' . now());
                                }
                                $success = true;
                            }
                        }
                    } else {
                        $element = new CampaignElement();
                        $element->is_conditional = 0;
                    }
                    if ($success || $action->current_element_id == 'step_1' || $current_time > $action->ending_time) {
                        $action->status = 'completed';
                        $action->save();
                        if ($action->next_true_element_id != '' || $action->next_false_element_id != '') {
                            if ($element->is_conditional == 1) {
                                if ($conditional_output == 'true' && $action->next_true_element_id != '') {
                                    $campaign_path = CampaignPath::where('current_element_id', $action->next_true_element_id)->first();
                                    $new_action = new LeadActions();
                                    $new_action->current_element_id = $action->next_true_element_id;
                                    if (!empty($campaign_path)) {
                                        $new_action->next_true_element_id = $campaign_path->next_true_element_id;
                                        $new_action->next_false_element_id = $campaign_path->next_false_element_id;
                                    } else {
                                        $new_action->next_true_element_id = '';
                                        $new_action->next_false_element_id = '';
                                    }
                                    $new_action->lead_id = $action->lead_id;
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
                                } else if ($conditional_output == 'false' && $action->next_false_element_id != '') {
                                    $campaign_path = CampaignPath::where('current_element_id', $action->next_false_element_id)->first();
                                    $new_action = new LeadActions();
                                    $new_action->current_element_id = $action->next_false_element_id;
                                    if (!empty($campaign_path)) {
                                        $new_action->next_true_element_id = $campaign_path->next_true_element_id;
                                        $new_action->next_false_element_id = $campaign_path->next_false_element_id;
                                    } else {
                                        $new_action->next_true_element_id = '';
                                        $new_action->next_false_element_id = '';
                                    }
                                    $new_action->lead_id = $action->lead_id;
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
                            } else {
                                $campaign_path = CampaignPath::where('current_element_id', $action->next_true_element_id)->first();
                                $new_action = new LeadActions();
                                $new_action->current_element_id = $action->next_true_element_id;
                                if (!empty($campaign_path)) {
                                    $new_action->next_true_element_id = $campaign_path->next_true_element_id;
                                    $new_action->next_false_element_id = $campaign_path->next_false_element_id;
                                } else {
                                    $new_action->next_true_element_id = '';
                                    $new_action->next_false_element_id = '';
                                }
                                $new_action->lead_id = $action->lead_id;
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
                        }
                    } else {
                        $this->error('Action: ' . $action->id . ' did not updated ' . ' at: ' . now());
                    }
                } else {
                    $this->error('No Campaign Found of Id: ' . $action->campaign_id . ' at: ' . now());
                }
            } catch (\Exception $e) {
                $this->error('Failed to insert data: ' . $e . ' at: ' . now());
            }
        }
    }
}
