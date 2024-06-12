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

class CampaignElementController extends Controller
{
    function campaignElement($slug)
    {
        if (Auth::check()) {
            $elements = CampaignElement::where('element_slug', $slug)->first();
            if ($elements) {
                $properties = ElementProperties::where('element_id', $elements->id)->get();
                if ($properties->isNotEmpty()) {
                    return response()->json(['success' => true, 'properties' => $properties]);
                } else {
                    return response()->json(['success' => false, 'message' => 'No Properties Found']);
                }
            }
        } else {
            return redirect(url('/'));
        }
    }

    function createCampaign(Request $request)
    {
        if (Auth::check()) {
            try {
                $user_id = Auth::user()->id;
                $seat_id = session('seat_id');
                $all_request = $request->all();
                $final_array = $all_request['final_array'];
                $final_data = $all_request['final_data'];
                $settings = $all_request['settings'];
                $img_path = $all_request['img_url'];
                $campaign = new Campaign();
                $campaign->campaign_name = $settings['campaign_name'];
                unset($settings['campaign_name']);
                $campaign->campaign_type = $settings['campaign_type'];
                unset($settings['campaign_type']);
                $campaign->campaign_url = $settings['campaign_url'];
                unset($settings['campaign_url']);
                $imported_lead = ImportedLeads::where('user_id', $user_id)->where('file_path', $settings['campaign_url_hidden'])->first();
                unset($settings['campaign_url_hidden']);
                if ($campaign->campaign_type != 'import' && $campaign->campaign_type != 'recruiter') {
                    $campaign->campaign_connection = $settings['connections'];
                    unset($settings['connections']);
                } else {
                    $campaign->campaign_connection = '0';
                }
                $campaign->user_id = $user_id;
                $campaign->seat_id = $seat_id;
                $campaign->description = '';
                $campaign->modified_date = date('Y-m-d');
                $campaign->start_date = date('Y-m-d');
                $campaign->end_date = date('Y-m-d');
                $campaign->img_path = $img_path;
                $campaign->save();
                if ($campaign->id) {
                    if (!empty($imported_lead)) {
                        $imported_lead->campaign_id = $campaign->id;
                        $imported_lead->save();
                    }
                    foreach ($settings as $key => $value) {
                        if (str_contains($key, 'email_settings_')) {
                            $setting = new EmailSetting();
                        }
                        if (str_contains($key, 'linkedin_settings_')) {
                            $setting = new LinkedinSetting();
                        }
                        if (str_contains($key, 'global_settings_')) {
                            $setting = new GlobalSetting();
                        }
                        $setting->campaign_id = $campaign->id;
                        $setting->setting_slug = $key;
                        $setting->user_id = $user_id;
                        $setting->seat_id = 1;
                        $setting->value = $value;
                        $setting->setting_name = ucwords(str_replace('_', ' ', $key));
                        $setting->save();
                    }
                    $path_array = [];
                    foreach ($final_array as $key => $value) {
                        if ($key != 'step' || $key != 'step-1') {
                            $element = CampaignElement::where('element_slug', $this->remove_prefix($key))->first();
                            if ($element) {
                                $element_item = new UpdatedCampaignElements();
                                $element_item->element_id = $element->id;
                                $element_item->campaign_id = $campaign->id;
                                $element_item->user_id = $user_id;
                                $element_item->seat_id = 1;
                                $element_item->position_x = $value['position_x'];
                                $element_item->position_y = $value['position_y'];
                                $element_item->element_slug = $key;
                                $element_item->save();
                                $path_array[$key] = $element_item->id;
                                if (isset($final_data[$key])) {
                                    $property_item = $final_data[$key];
                                    foreach ($property_item as $key => $value) {
                                        $element_property = new UpdatedCampaignProperties();
                                        $property = ElementProperties::where('id', $key)->first();
                                        if ($property) {
                                            $element_property->element_id = $element_item->id;
                                            $element_property->property_id = $property->id;
                                            $element_property->campaign_id = $campaign->id;
                                            if ($value != null) {
                                                $element_property->value = $value;
                                            } else {
                                                $element_property->value = '';
                                            }
                                            $element_property->save();
                                        } else {
                                            LinkedinSetting::where('campaign_id', $campaign->id)->delete();
                                            LeadActions::where('campaign_id', $campaign->id)->delete();
                                            Leads::where('campaign_id', $campaign->id)->delete();
                                            ImportedLeads::where('campaign_id', $campaign->id)->delete();
                                            GlobalSetting::where('campaign_id', $campaign->id)->delete();
                                            EmailSetting::where('campaign_id', $campaign->id)->delete();
                                            UpdatedCampaignProperties::where('campaign_id', $campaign->id)->delete();
                                            CampaignPath::where('campaign_id', $campaign->id)->delete();
                                            UpdatedCampaignElements::where('campaign_id', $campaign->id)->delete();
                                            CampaignActions::where('campaign_id', $campaign->id)->delete();
                                            $campaign->delete();
                                            return response()->json(['success' => false, 'properties' => 'Properties not found!']);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    foreach ($final_array as $key => $value) {
                        if (isset($path_array[$key])) {
                            $path = new CampaignPath();
                            $path->campaign_id = $campaign->id;
                            $path->current_element_id = $path_array[$key];
                            if ($final_array[$key]['0'] == '' && $final_array[$key]['1'] == '') {
                                $path->next_false_element_id = '';
                                $path->next_true_element_id = '';
                            } else if ($final_array[$key]['0'] == '') {
                                $path->next_true_element_id = $path_array[$value['1']];
                                $path->next_false_element_id = '';
                            } else if ($final_array[$key]['1'] == '') {
                                $path->next_true_element_id = '';
                                $path->next_false_element_id = $path_array[$value['0']];
                            } else {
                                $path->next_true_element_id = $path_array[$value['1']];
                                $path->next_false_element_id = $path_array[$value['0']];
                            }
                            $path->save();
                        }
                    }
                    $action = new CampaignActions();
                    $campaign_path = CampaignPath::where('campaign_id', $campaign->id)->first();
                    $action->current_element_id = 'step_1';
                    $action->next_true_element_id = $campaign_path->current_element_id;
                    $action->next_false_element_id = '';
                    $action->created_at = now();
                    $action->updated_at = now();
                    $action->campaign_id = $campaign->id;
                    $action->status = 'inprogress';
                    $action->ending_time = now();
                    $action->save();
                    $request->session()->flash('success', 'Campaign succesfully saved!');
                    return response()->json(['success' => true]);
                } else {
                    return response()->json(['success' => false, 'message' => 'No Campaign Inserted']);
                }
            } catch (\Exception $e) {
                LinkedinSetting::where('campaign_id', $campaign->id)->delete();
                LeadActions::where('campaign_id', $campaign->id)->delete();
                Leads::where('campaign_id', $campaign->id)->delete();
                ImportedLeads::where('campaign_id', $campaign->id)->delete();
                GlobalSetting::where('campaign_id', $campaign->id)->delete();
                EmailSetting::where('campaign_id', $campaign->id)->delete();
                UpdatedCampaignProperties::where('campaign_id', $campaign->id)->delete();
                CampaignPath::where('campaign_id', $campaign->id)->delete();
                UpdatedCampaignElements::where('campaign_id', $campaign->id)->delete();
                CampaignActions::where('campaign_id', $campaign->id)->delete();
                $campaign->delete();
                return response()->json(['success' => false, 'properties' => $e]);
            }
        } else {
            return redirect(url('/'));
        }
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
        if (Auth::check()) {
            $elements = UpdatedCampaignElements::where('campaign_id', $campaign_id)->orderBy('id')->get();
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
    }

    function getcampaignelementbyid($element_id)
    {
        if (Auth::check()) {
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
        } else {
            return redirect(url('/'));
        }
    }
}
