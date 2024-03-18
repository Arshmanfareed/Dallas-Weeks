<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FeatureController extends Controller
{
   function featuresuggestions(){
    $data=[
        'title'=>'Feature Suggestions'
    ];
    return view('feature',$data);
   }
}
