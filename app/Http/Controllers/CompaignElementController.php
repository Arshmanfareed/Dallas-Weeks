<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignElement;
use App\Models\ElementProperties;
use App\Models\LinkedinSetting;
use App\Models\UpdatedCompaignElements;
use App\Models\UpdatedCompaignProperties;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompaignElementController extends Controller
{
    function compaignElement($slug)
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
    function createCompaign(Request $request)
    {
        $all_request = $request->all();
        $final_array = $all_request['final_array'];
        $final_data = $all_request['final_data'];
        $linkedin_setting = $all_request['linkedin_setting'];
        $user_id = Auth::user()->id;
        if ($user_id) {
            $compaign = new Campaign();
            $compaign->compaign_name = 'My Compaign';
            $compaign->user_id = $user_id;
            $compaign->seat_id = 1;
            $compaign->description = 'This compaign is the test compaign';
            $compaign->modified_date = date('Y-m-d');
            $compaign->start_date = date('Y-m-d');
            $compaign->end_date = date('Y-m-d');
            $compaign->save();
            if ($compaign->id) {
                foreach ($linkedin_setting as $key => $value) {
                    $setting = new LinkedinSetting();
                    $setting->compaign_id = $compaign->id;
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
                $elements = [];
                foreach ($final_array as $value) {
                    $reverse = strrev($value);
                    $first_index = strpos($reverse, '_');
                    $second_index = strlen($value) - $first_index - 1;
                    $string = substr($value, 0, $second_index);
                    $elements[] = $string;
                }
                $count = 0;
                foreach ($elements as $value) {
                    if ($value != 'step' || $value != 'step-1') {
                        $element = CampaignElement::where('element_slug', $value)->first();
                        if ($element) {
                            $element_item = new UpdatedCompaignElements();
                            $element_item->element_id = $element->id;
                            $element_item->compaign_id = $compaign->id;
                            $element_item->compaign_element_id = ++$count;
                            $element_item->user_id = $user_id;
                            $element_item->seat_id = 1;
                            $element_item->save();
                            $property_item = $final_data[$final_array[$count]];
                            if ($property_item) {
                                foreach ($property_item as $key => $value) {
                                    $element_property = new UpdatedCompaignProperties();
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
                            } else {
                                return response()->json(['success' => false, 'properties' => 'Element not found!']);
                            }
                        }
                    }
                }
            }
            return response()->json(['success' => true, 'properties' => 'Changes succesfully updated']);
        } else {
            return response()->json(['success' => false, 'properties' => 'User login first!']);
        }
    }
}
