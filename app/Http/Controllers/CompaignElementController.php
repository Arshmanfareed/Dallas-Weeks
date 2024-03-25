<?php

namespace App\Http\Controllers;

use App\Models\CampaignElement;
use App\Models\ElementProperties;
use Illuminate\Http\Request;

class CompaignElementController extends Controller
{
    function compaignElement($slug)
    {
        $elements = CampaignElement::where('element_slug', $slug)->first();
        if ($elements) {
            $properties = ElementProperties::where('element_id', $elements->id)->get();
            if ($properties->isNotEmpty()) {
                return response()->json(['success'=>true, 'properties'=>$properties]);
            } else {
                return response()->json(['success'=>false, 'message'=>'No Properties Found']);
            }
        }
    }
}
