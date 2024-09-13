<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignActions;
use App\Models\CampaignElement;
use App\Models\CampaignPath;
use App\Models\ElementProperties;
use App\Models\EmailSetting;
use App\Models\GlobalSetting;
use App\Models\ImportedLeads;
use App\Models\LeadActions;
use App\Models\Leads;
use App\Models\LinkedinSetting;
use App\Models\UpdatedCampaignElements;
use App\Models\UpdatedCampaignProperties;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\CampaignSchedule;
use Illuminate\Support\Facades\Validator;
use App\Models\SeatInfo;
use Illuminate\Http\JsonResponse;
use Exception;
use App\Models\Roles;
use App\Models\Teams;
use App\Models\AssignedSeats;
use App\Models\SeatEmail;
use Illuminate\Support\Facades\Log;
use App\Models\Role_Permission;
use App\Models\Permissions;

class CampaignElementController extends Controller
{
    function campaignElement($slug)
    {
        try {
            /* Get seat_id from the request or session, if not found, throw an exception */
            $seat_id = session('seat_id');

            /* Get the authenticated user */
            $user = Auth::user();

            /* Retrieve the team record associated with the user's team ID */
            $team = Teams::find($user->team_id);

            /* Find the seat for the user based on the provided seat ID */
            $seat = SeatInfo::where('team_id', $team->id)->where('id', $seat_id)->first();

            /* Find the assigned seat for the user (either seat_id is 0 or matching the seat ID) */
            $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat->id])
                ->where('user_id', $user->id)
                ->first();
            /* Get the user's role based on the assigned seat */
            $role = Roles::find($assignedSeat->role_id);

            $data['manage_campaigns'] = $this->checkPermission($role->id, 'manage_campaigns');
            $data['manage_webhooks'] = $this->checkPermission($role->id, 'manage_webhooks');
            $data['manage_linkedin_integrations'] = $this->checkPermission($role->id, 'manage_linkedin_integrations');
            $data['manage_email_settings'] = $this->checkPermission($role->id, 'manage_email_settings');
            $data['manage_global_limits'] = $this->checkPermission($role->id, 'manage_global_limits');
            $data['manage_account_health'] = $this->checkPermission($role->id, 'manage_account_health');
            $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
            $data['manage_chat'] = $this->checkPermission($role->id, 'manage_chat');
            $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
            $elements = CampaignElement::where('element_slug', $slug)->first();
            if ($elements) {
                $properties = ElementProperties::where('element_id', $elements->id)->get();
                if ($properties->isNotEmpty()) {
                    return response()->json(['success' => true, 'properties' => $properties]);
                } else {
                    return response()->json(['success' => false, 'message' => 'No Properties Found']);
                }
            }
        } catch (Exception $e) {
            Log::info($e);
            return redirect()->route('acc_dash')->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function createCampaign(Request $request)
    {
        try {
            /* Get seat_id from the request or session, if not found, throw an exception */
            $seat_id = session('seat_id');

            /* Get the authenticated user */
            $user = Auth::user();

            /* Retrieve the team record associated with the user's team ID */
            $team = Teams::find($user->team_id);

            /* Find the seat for the user based on the provided seat ID */
            $seat = SeatInfo::where('team_id', $team->id)->where('id', $seat_id)->first();

            /* Find the assigned seat for the user (either seat_id is 0 or matching the seat ID) */
            $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat->id])
                ->where('user_id', $user->id)
                ->first();
            /* Get the user's role based on the assigned seat */
            $role = Roles::find($assignedSeat->role_id);

            $data['manage_campaigns'] = $this->checkPermission($role->id, 'manage_campaigns');
            $data['manage_webhooks'] = $this->checkPermission($role->id, 'manage_webhooks');
            $data['manage_linkedin_integrations'] = $this->checkPermission($role->id, 'manage_linkedin_integrations');
            $data['manage_email_settings'] = $this->checkPermission($role->id, 'manage_email_settings');
            $data['manage_global_limits'] = $this->checkPermission($role->id, 'manage_global_limits');
            $data['manage_account_health'] = $this->checkPermission($role->id, 'manage_account_health');
            $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
            $data['manage_chat'] = $this->checkPermission($role->id, 'manage_chat');
            $data['manage_campaign_details_and_reports'] = $this->checkPermission($role->id, 'manage_campaign_details_and_reports');
            DB::beginTransaction();
            $campaign = null;
            try {
                $user_id = Auth::user()->id;
                $seat_id = session('seat_id');
                $data = $request->all();
                $final_array = $data['final_array'];
                $final_data = $data['final_data'];
                $settings = $data['settings'];
                $img_path = $data['img_url'];
                $oneMinuteAgo = Carbon::now()->subMinute();
                $existing_campaign = Campaign::where('campaign_name', $settings['campaign_name'])
                    ->where('user_id', $user_id)
                    ->where('seat_id', $seat_id)
                    ->where('created_at', '>=', $oneMinuteAgo)
                    ->first();
                if ($existing_campaign) {
                    $request->session()->flash('success', 'Campaign successfully saved!');
                    return response()->json(['success' => true]);
                }
                $campaign = new Campaign([
                    'campaign_name' => $settings['campaign_name'],
                    'campaign_type' => $settings['campaign_type'],
                    'campaign_url' => $settings['campaign_url'],
                    'campaign_connection' => ($settings['campaign_type'] != 'import' && $settings['campaign_type'] != 'recruiter' && $settings['campaign_type'] != 'leads_list') ? $settings['connections'] : 'o',
                    'user_id' => $user_id,
                    'seat_id' => $seat_id,
                    'modified_date' => now()->format('Y-m-d'),
                    'start_date' => now()->format('Y-m-d'),
                    'end_date' => now()->format('Y-m-d'),
                    'img_path' => $img_path
                ]);
                $campaign->save();
                if (!empty($settings['campaign_url_hidden'])) {
                    $imported_lead = ImportedLeads::where('user_id', $user_id)
                        ->where('file_path', $settings['campaign_url_hidden'])
                        ->first();
                    if (!empty($imported_lead)) {
                        $imported_lead->update(['campaign_id' => $campaign->id]);
                        $campaign['campaign_url'] = $imported_lead['file_path'];
                        $campaign->save();
                    }
                }
                $this->saveSettings($settings, $campaign->id, $user_id);
                $this->saveCampaignElements($final_array, $final_data, $campaign->id, $user_id);
                $this->createInitialCampaignAction($campaign->id);
                DB::commit();
                $request->session()->flash('success', 'Campaign successfully saved!');
                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                DB::rollBack();
                if ($campaign !== null) {
                    $this->deleteCampaignData($campaign->id);
                }
                return response()->json(['success' => false, 'message' => 'Campaign save unsuccesfull']);
            }
        } catch (Exception $e) {
            Log::info($e);
            return redirect()->route('acc_dash')->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function saveSettings($settings, $campaign_id, $user_id)
    {
        foreach ($settings as $key => $value) {
            $setting = $this->getSettingModel($key);
            if ($setting) {
                $setting->create([
                    'campaign_id' => $campaign_id,
                    'setting_slug' => $key,
                    'user_id' => $user_id,
                    'seat_id' => 1,
                    'value' => $value,
                    'setting_name' => ucwords(str_replace('_', ' ', $key)),
                ]);
            }
        }
    }

    private function getSettingModel($key)
    {
        if (str_contains($key, 'email_settings_')) {
            return new EmailSetting();
        }
        if (str_contains($key, 'linkedin_settings_')) {
            return new LinkedinSetting();
        }
        if (str_contains($key, 'global_settings_')) {
            return new GlobalSetting();
        }
        return null;
    }

    private function saveCampaignElements($final_array, $final_data, $campaign_id, $user_id)
    {
        $time = now();
        $path_array = [];
        foreach ($final_array as $key => $value) {
            if ($key != 'step' && $key != 'step-1') {
                $element = CampaignElement::where('element_slug', $this->remove_prefix($key))->first();
                if ($element) {
                    $element_item = UpdatedCampaignElements::create([
                        'element_id' => $element->id,
                        'campaign_id' => $campaign_id,
                        'user_id' => $user_id,
                        'seat_id' => 1,
                        'position_x' => $value['position_x'],
                        'position_y' => $value['position_y'],
                        'element_slug' => $key,
                    ]);
                    $path_array[$key] = $element_item->id;
                    if (isset($final_data[$key])) {
                        $this->saveElementProperties($element_item->id, $final_data[$key], $campaign_id, $time);
                    }
                }
            }
        }
        Campaign::where('id', $campaign_id)->update(['end_date' => $time]);
        foreach ($final_array as $key => $value) {
            if (isset($path_array[$key])) {
                CampaignPath::create([
                    'campaign_id' => $campaign_id,
                    'current_element_id' => $path_array[$key],
                    'next_false_element_id' => $final_array[$key]['0'] ? $path_array[$value['0']] : '',
                    'next_true_element_id' => $final_array[$key]['1'] ? $path_array[$value['1']] : '',
                ]);
            }
        }
    }

    private function saveElementProperties($element_item_id, $property_item, $campaign_id, &$time)
    {
        foreach ($property_item as $property_id => $value) {
            $property = ElementProperties::find($property_id);

            if ($property) {
                $element_property = UpdatedCampaignProperties::create([
                    'element_id' => $element_item_id,
                    'property_id' => $property_id,
                    'campaign_id' => $campaign_id,
                    'value' => $value ?? '',
                ]);

                if ($element_property->value) {
                    $timeToAdd = intval($element_property->value);
                    if ($property->property_name == 'Hours') {
                        $time->addHours($timeToAdd);
                    } elseif ($property->property_name == 'Days') {
                        $time->addDays($timeToAdd);
                    }
                }
            }
        }
    }

    private function createInitialCampaignAction($campaign_id)
    {
        $campaign_path = CampaignPath::where('campaign_id', $campaign_id)->first();
        CampaignActions::create([
            'current_element_id' => 'step_1',
            'next_true_element_id' => $campaign_path->current_element_id,
            'next_false_element_id' => '',
            'created_at' => now(),
            'updated_at' => now(),
            'campaign_id' => $campaign_id,
            'status' => 'inprogress',
            'ending_time' => now(),
        ]);
    }

    private function deleteCampaignData($campaign_id)
    {
        LinkedinSetting::where('campaign_id', $campaign_id)->delete();
        LeadActions::where('campaign_id', $campaign_id)->delete();
        Leads::where('campaign_id', $campaign_id)->delete();
        ImportedLeads::where('campaign_id', $campaign_id)->delete();
        GlobalSetting::where('campaign_id', $campaign_id)->delete();
        EmailSetting::where('campaign_id', $campaign_id)->delete();
        UpdatedCampaignProperties::where('campaign_id', $campaign_id)->delete();
        CampaignPath::where('campaign_id', $campaign_id)->delete();
        UpdatedCampaignElements::where('campaign_id', $campaign_id)->delete();
        CampaignActions::where('campaign_id', $campaign_id)->delete();
        Campaign::where('id', $campaign_id)->delete();
    }

    private function remove_prefix($value)
    {
        $reverse = strrev($value);
        $first_index = strpos($reverse, '_');
        $second_index = strlen($value) - $first_index - 1;
        $string = substr($value, 0, $second_index);
        return $string;
    }

    function getElements($campaign_id)
    {
        $elements = UpdatedCampaignElements::where('campaign_id', $campaign_id)->orderBy('id')->get();
        if (Auth::check()) {
            foreach ($elements as $element) {
                $element['original_element'] = CampaignElement::where('id', $element->element_id)->first();
                $element['properties'] = UpdatedCampaignProperties::where('element_id', $element->id)->get();
                foreach ($element['properties'] as $property) {
                    $property['original_properties'] = ElementProperties::where('id', $property->property_id)->first();
                }
            }
            $path = CampaignPath::where('campaign_id', $campaign_id)->orderBy('id')->get();
            return response()->json(['success' => true, 'elements_array' => $elements, 'path' => $path]);
        } else {
            return redirect(url('/'));
        }
        $path = CampaignPath::where('campaign_id', $campaign_id)->orderBy('id')->get();
        return response()->json(['success' => true, 'elements_array' => $elements, 'path' => $path]);
    }

    function getcampaignelementbyid($element_id)
    {
        try {
            /* Get seat_id from the request or session, if not found, throw an exception */
            $seat_id = session('seat_id');

            /* Get the authenticated user */
            $user = Auth::user();

            /* Retrieve the team record associated with the user's team ID */
            $team = Teams::find($user->team_id);

            /* Find the seat for the user based on the provided seat ID */
            $seat = SeatInfo::where('team_id', $team->id)->where('id', $seat_id)->first();

            /* Find the assigned seat for the user (either seat_id is 0 or matching the seat ID) */
            $assignedSeat = AssignedSeats::whereIn('seat_id', [0, $seat->id])
                ->where('user_id', $user->id)
                ->first();
            /* Get the user's role based on the assigned seat */
            $role = Roles::find($assignedSeat->role_id);

            $data['manage_campaigns'] = $this->checkPermission($role->id, 'manage_campaigns');
            $properties = UpdatedCampaignProperties::where('element_id', $element_id)->get();
            if ($properties->isNotEmpty()) {
                foreach ($properties as $property) {
                    $property['original_properties'] = ElementProperties::where('id', $property->property_id)->first();
                }
                return response()->json(['success' => true, 'properties' => $properties]);
            } else {
                $element = CampaignElement::where('element_slug', $this->remove_prefix($element_id))->first();
                $properties = ElementProperties::where('element_id', $element->id)->get();
                return response()->json(['success' => false, 'properties' => $properties]);
            }
        } catch (Exception $e) {
            Log::info($e);
            return redirect()->route('acc_dash')->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Check if the role has a given permission and return the access level.
     *
     * @param int $role_id The role ID to check.
     * @param string $permission_slug The permission slug to check.
     * @return bool|string True for full access, 'view_only' for view-only access, false for no access.
     */
    private function checkPermission($role_id, $permission_slug)
    {
        /* Fetch the permission for the given slug */
        $permission = Permissions::where('permission_slug', 'like', '%' . $permission_slug . '%')->first();

        if (!$permission) {
            return false;
        }

        /* Fetch the role permission relation */
        $rolePermission = Role_Permission::where('permission_id', $permission->id)
            ->where('role_id', $role_id)
            ->first();

        /* Check access and return appropriate status */
        if ($rolePermission && $rolePermission->view_only == 1) {
            return 'view_only';
        } elseif ($rolePermission && $rolePermission->access == 1) {
            return true;
        }

        return false;
    }
}
