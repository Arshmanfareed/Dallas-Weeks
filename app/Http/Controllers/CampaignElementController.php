<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignElement;
use App\Models\CampaignPath;
use App\Models\ElementProperties;
use App\Models\LinkedinSetting;
use App\Models\UpdatedCampaignElements;
use App\Models\UpdatedCampaignProperties;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignElementController extends Controller
{
    function campaignElement($slug)
    {
        $elements = CampaignElement::where('element_slug', $slug)->first();
        if ($elements) {
            $properties = ElementProperties::where('element_id', $elements->id)->get();
            if ($properties->isNotEmpty()) {
                return response()->json(['success' => true, 'properties' => $properties]);
            } else {
                return response()->json(['success' => false, 'message' => 'No Properties Found']);
            }
        }
    }
    function createCampaign(Request $request)
    {
        $all_request = $request->all();
        $final_array = $all_request['final_array'];
        $final_data = $all_request['final_data'];
        $linkedin_setting = $all_request['linkedin_setting'];
        $user_id = Auth::user()->id;
        if ($user_id) {
            $campaign = new Campaign();
            $campaign->campaign_name = $linkedin_setting['campaign_name'];
            unset($linkedin_setting['campaign_name']);
            $campaign->campaign_type = $linkedin_setting['campaign_type'];
            unset($linkedin_setting['campaign_type']);
            $campaign->campaign_url = $linkedin_setting['campaign_url'];
            unset($linkedin_setting['campaign_url']);
            $campaign->campaign_connection = $linkedin_setting['connections'];
            unset($linkedin_setting['connections']);
            $campaign->user_id = $user_id;
            $campaign->seat_id = 1;
            $campaign->description = 'This campaign is the test campaign';
            $campaign->modified_date = date('Y-m-d');
            $campaign->start_date = date('Y-m-d');
            $campaign->end_date = date('Y-m-d');
            $campaign->save();
            if ($campaign->id) {
                foreach ($linkedin_setting as $key => $value) {
                    $setting = new LinkedinSetting();
                    $setting->campaign_id = $campaign->id;
                    $setting->setting_slug = $key;
                    $setting->user_id = $user_id;
                    $setting->seat_id = 1;
                    if ($value == 'false') {
                        $setting->is_active = 0;
                    } else {
                        $setting->is_active = 1;
                    }
                    $setting->setting_name = ucwords(str_replace('_', ' ', $key));
                    $setting->save();
                }
                $path_array = [];
                $count = 0;
                foreach ($final_array as $key => $value) {
                    if ($key != 'step' || $key != 'step-1') {
                        $element = CampaignElement::where('element_slug', $this->remove_prefix($key))->first();
                        if ($element) {
                            $element_item = new UpdatedCampaignElements();
                            $element_item->element_id = $element->id;
                            $element_item->campaign_id = $campaign->id;
                            $element_item->campaign_element_id = ++$count;
                            $element_item->user_id = $user_id;
                            $element_item->seat_id = 1;
                            $element_item->save();
                            $path_array[$key] = $element_item->id;
                            if (isset($final_data[$key])) {
                                $property_item = $final_data[$key];
                                foreach ($property_item as $key => $value) {
                                    $element_property = new UpdatedCampaignProperties();
                                    $property = ElementProperties::where('property_name', $key)->first();
                                    if ($property) {
                                        $element_property->element_id = $element_item->id;
                                        $element_property->property_id = $property->id;
                                        $element_property->value = $value;
                                        $element_property->save();
                                    } else {
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
                        $path->current_element_id = $path_array[$key];
                        if ($final_array[$key]['0'] == '' && $final_array[$key]['1'] == '') {
                            continue;
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
            }
            $request->session()->flash('success', 'Campaign succesfully saved!');
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'properties' => 'User login first!']);
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
}
