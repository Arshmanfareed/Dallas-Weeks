<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignElement;
use App\Models\ElementProperties;
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
        $compaign = new Campaign();
        $compaign->compaign_name = 'My Compaign';
        $id = Auth::user()->id;
        if ($id) {
            $compaign->user_id = $id;
            $compaign->seat_id = 1;
            $compaign->description = 'This compaign is the test compaign';
            $compaign->modified_date = date('Y-m-d');
            $compaign->start_date = date('Y-m-d');
            $compaign->end_date = date('Y-m-d');
            $compaign->save();
            $elements = [];
            foreach ($final_array as $value) {
                $position = strlen($value) - 2;
                $string = substr_replace($value, '', $position, 2);
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
                        $element_item->user_id = $id;
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
            return response()->json(['success' => true, 'properties' => 'Changes succesfully updated']);
        } else {
            return response()->json(['success' => false, 'properties' => 'User login first!']);
        }
    }
}
