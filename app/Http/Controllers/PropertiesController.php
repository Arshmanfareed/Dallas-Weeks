<?php

namespace App\Http\Controllers;

use App\Models\CampaignElement;
use App\Models\ElementProperties;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertiesController extends Controller
{
    function getPropertyDatatype($id, $element_slug)
    {
        $string = $element_slug;
        $element = CampaignElement::where('element_slug', $string)->first();
        if ($element) {
            $property = ElementProperties::where('element_id', $element->id)->where('id', $id)->first();
            if ($property) {
                return response()->json(['success' => true, 'property' => $property]);
            } else {
                return response()->json(['success' => false, 'property' => 'Properties not found!']);
            }
        } else {
            return response()->json(['success' => false, 'properties' => 'Element not found!' . $string]);
        }
    }
    
    function getPropertyRequired($id)
    {
        $property = ElementProperties::where('id', $id)->first();
        if ($property) {
            return response()->json(['success' => true, 'property' => $property]);
        } else {
            return response()->json(['success' => false, 'property' => 'Properties not found!']);
        }
    }
}
