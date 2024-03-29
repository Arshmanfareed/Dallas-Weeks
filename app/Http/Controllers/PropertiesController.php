<?php

namespace App\Http\Controllers;

use App\Models\CampaignElement;
use App\Models\ElementProperties;
use Illuminate\Http\Request;

class PropertiesController extends Controller
{
    function getPropertyDatatype($name, $element_slug)
    {
        $string = $element_slug;
        $element = CampaignElement::where('element_slug', $string)->first();
        if ($element) {
            $property = ElementProperties::where('element_id', $element->id)->where('property_name', $name)->first();
            if ($property) {
                return response()->json(['success' => true, 'properties' => $property->data_type]);
            } else {
                return response()->json(['success' => false, 'properties' => 'Properties not found!']);
            }
        } else {
            return response()->json(['success' => false, 'properties' => 'Element not found!' . $string]);
        }
    }
}
