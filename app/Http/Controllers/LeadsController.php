<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LeadsController extends Controller
{
   function leads(){
    $data=[
        'title'=>'Leads'
    ];
    return view('leads',$data);
   }
}
