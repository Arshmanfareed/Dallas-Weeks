<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Leads;

class LeadsController extends Controller
{
   function leads(){
    $user_id = Auth::user()->id;
    $leads = Leads::where('user_id', $user_id)->get();
    $data=[
        'title'=>'Leads',
        'leads' => $leads,
    ];
    return view('leads',$data);
   }
}
