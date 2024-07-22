<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Models\CampaignElement;
use App\Models\CampaignPath;
use App\Models\ElementProperties;
use App\Models\LeadActions;
use App\Http\Controllers\CronController;
use App\Http\Controllers\LeadsController;
use App\Models\SeatInfo;
use App\Models\UpdatedCampaignElements;
use App\Models\UpdatedCampaignProperties;
use App\Http\Controllers\SeatController;

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
        $logFilePath = storage_path('logs/lead_action.log');
        file_put_contents($logFilePath, 'Action Leads started at: ' . now() . PHP_EOL, FILE_APPEND);
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
                    foreach ($campaigns as $campaign) {
                        $actions = LeadActions::where('campaign_id', $campaign['id'])->where('status', 'inprogress')->get();
                        $view_count = 0;
                        $invite_count = 0;
                        $message_count = 0;
                        foreach ($actions as $action) {
                            try {
                                $success = false;
                                $conditional_output = '';
                                if ($action['current_element_id'] != 'step_1') {
                                    $campaign_element = UpdatedCampaignElements::where('id', $action['current_element_id'])->first();
                                    $element = CampaignElement::where('id', $campaign_element['element_id'])->first();
                                    $seat = SeatInfo::where('id', $campaign['seat_id'])->first();
                                    $account_id = $seat['account_id'];
                                    if ($current_time <= $action['ending_time']) {
                                        $cc = new CronController();
                                        switch ($element['element_slug']) {
                                            case 'view_profile':
                                                if ($view_count < $view_distribution_limit) {
                                                    $success = $cc->view_profile($action, $account_id);
                                                    if ($success) {
                                                        file_put_contents($logFilePath, 'Profile viewed successfully at: ' . now() . PHP_EOL, FILE_APPEND);
                                                    }
                                                    $view_count++;
                                                } else if ($view_distribution_limit == 0) {
                                                    file_put_contents($logFilePath, 'Failed to insert data because allowed View Profile is 0' . ' at: ' . now() . PHP_EOL, FILE_APPEND);
                                                }
                                                break;
                                            case 'invite_to_connect':
                                                if ($invite_count < $invitation_distribution_limit) {
                                                    $success = $cc->invite_to_connect($action, $account_id, $element, $campaign_element);
                                                    if ($success) {
                                                        file_put_contents($logFilePath, 'Invitation to connect sent successfully at: ' . now() . PHP_EOL, FILE_APPEND);
                                                    }
                                                    $invite_count++;
                                                } else if ($invitation_distribution_limit == 0) {
                                                    file_put_contents($logFilePath, 'Failed to insert data because allowed Invite to connect is 0' . ' at: ' . now() . PHP_EOL, FILE_APPEND);
                                                }
                                                break;
                                            case 'message':
                                                if ($message_count < $message_distribution_limit) {
                                                    $success = $cc->message($action, $account_id, $element, $campaign_element);
                                                    if ($success) {
                                                        file_put_contents($logFilePath, 'Message sent successfully at: ' . now() . PHP_EOL, FILE_APPEND);
                                                    }
                                                    $message_count++;
                                                } else if ($message_distribution_limit == 0) {
                                                    file_put_contents($logFilePath, 'Failed to insert data because allowed Message is 0' . ' at: ' . now() . PHP_EOL, FILE_APPEND);
                                                }
                                                break;
                                            case 'inmail_message':
                                                $success = $cc->inmail_message($action, $account_id, $element, $campaign_element);
                                                if ($success) {
                                                    file_put_contents($logFilePath, 'Inmail message sent successfully at: ' . now() . PHP_EOL, FILE_APPEND);
                                                }
                                                break;
                                            case 'follow':
                                                $success = $cc->follow($action, $account_id);
                                                if ($success) {
                                                    file_put_contents($logFilePath, 'Follow user successfully at: ' . now() . PHP_EOL, FILE_APPEND);
                                                }
                                                break;
                                            case 'email_message':
                                                $success = $cc->email_message($action, $account_id, $element, $campaign_element);
                                                if ($success) {
                                                    file_put_contents($logFilePath, 'Email sent successfully at: ' . now() . PHP_EOL, FILE_APPEND);
                                                }
                                                break;
                                            case 'if_connected':
                                                $conditional_output = $cc->if_connected($action, $account_id);
                                                if ($conditional_output == 'true') {
                                                    file_put_contents($logFilePath, 'Lead is already connected at: ' . now() . PHP_EOL, FILE_APPEND);
                                                } else {
                                                    file_put_contents($logFilePath, 'Lead is not connected at: ' . now() . PHP_EOL, FILE_APPEND);
                                                }
                                                $success = true;
                                                break;
                                            case 'if_email_is_opened':
                                                $conditional_output = $cc->if_email_is_opened($action);
                                                if ($conditional_output == 'true') {
                                                    file_put_contents($logFilePath, 'Email is already opened at: ' . now() . PHP_EOL, FILE_APPEND);
                                                } else {
                                                    file_put_contents($logFilePath, 'Email is not opened at: ' . now() . PHP_EOL, FILE_APPEND);
                                                }
                                                $success = true;
                                                break;
                                            case 'if_has_imported_email':
                                                $conditional_output = $cc->if_has_imported_email($action);
                                                if ($conditional_output == 'true') {
                                                    file_put_contents($logFilePath, 'Email is already opened at: ' . now() . PHP_EOL, FILE_APPEND);
                                                } else {
                                                    file_put_contents($logFilePath, 'Email is not opened at: ' . now() . PHP_EOL, FILE_APPEND);
                                                }
                                                $success = true;
                                                break;
                                            case 'if_has_verified_email':
                                                $conditional_output = $cc->if_has_verified_email($action);
                                                if ($conditional_output == 'true') {
                                                    file_put_contents($logFilePath, 'Email is already verified at: ' . now() . PHP_EOL, FILE_APPEND);
                                                } else {
                                                    file_put_contents($logFilePath, 'Email is not verified at: ' . now() . PHP_EOL, FILE_APPEND);
                                                }
                                                $success = true;
                                                break;
                                            case 'if_free_inmail':
                                                $conditional_output = $cc->if_free_inmail($action);
                                                if ($conditional_output == 'true') {
                                                    file_put_contents($logFilePath, 'Inmail is already free at: ' . now() . PHP_EOL, FILE_APPEND);
                                                } else {
                                                    file_put_contents($logFilePath, 'Inmail is not free at: ' . now() . PHP_EOL, FILE_APPEND);
                                                }
                                                $success = true;
                                                break;
                                        }
                                    }
                                } else {
                                    $element = new CampaignElement();
                                    $element['is_conditional'] = 0;
                                }
                                if ($success || $action['current_element_id'] == 'step_1' || $current_time > $action['ending_time']) {
                                    $action['status'] = 'completed';
                                    $action['updated_at'] = now();
                                    $action->save();
                                    $this->handleNextActions($action, $element, $conditional_output, $campaign);
                                }
                            } catch (\Exception $e) {
                                file_put_contents($logFilePath, 'Failed to insert data because ' . $e->getMessage() . ' at: ' . now() . PHP_EOL, FILE_APPEND);
                            }
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

    private function handleNextActions($action, $element, $conditional_output, $campaign)
    {
        if ($action['next_true_element_id'] != '' || $action['next_false_element_id'] != '') {
            if ($element['is_conditional'] == 1) {
                if ($conditional_output == 'true' && $action['next_true_element_id'] != '') {
                    $this->createNewAction($action['next_true_element_id'], $action, $campaign);
                } else if ($conditional_output == 'false' && $action['next_false_element_id'] != '') {
                    $this->createNewAction($action['next_false_element_id'], $action, $campaign);
                }
            } else {
                $this->createNewAction($action['next_true_element_id'], $action, $campaign);
            }
        }
    }

    private function createNewAction($next_element_id, $action, $campaign)
    {
        $campaign_path = CampaignPath::where('current_element_id', $next_element_id)->first();
        $new_action = new LeadActions();
        $new_action['current_element_id'] = $next_element_id;
        if (!empty($campaign_path)) {
            $new_action['next_true_element_id'] = $campaign_path['next_true_element_id'];
            $new_action['next_false_element_id'] = $campaign_path['next_false_element_id'];
        } else {
            $new_action['next_true_element_id'] = '';
            $new_action['next_false_element_id'] = '';
        }
        $new_action['lead_id'] = $action['lead_id'];
        $new_action['created_at'] = now();
        $new_action['updated_at'] = now();
        $new_action['campaign_id'] = $campaign['id'];
        $new_action['status'] = 'inprogress';
        $properties = UpdatedCampaignProperties::where('element_id', $new_action['current_element_id'])->get();
        $time = now();
        foreach ($properties as $property) {
            $campaign_property = ElementProperties::where('id', $property['property_id'])->first();
            if (!empty($campaign_property) && isset($property['value'])) {
                $timeToAdd = intval($property['value']);
                if ($campaign_property['property_name'] == 'Hours') {
                    $time->modify('+' . $timeToAdd . ' hours');
                } else if ($campaign_property['property_name'] == 'Days') {
                    $time->modify('+' . $timeToAdd . ' days');
                }
            }
        }
        $new_action['ending_time'] = $time->format('Y-m-d H:i:s');
        $new_action->save();
    }
}
