<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Campaign;

class MaindashboardController extends Controller
{
   function maindasboard(){
    $user_id = Auth::user()->id;
    $compaigns = Campaign::where('user_id', $user_id)->get();
    $data=[
        'title'=>'Account Dashboard',
        'compaigns' => $compaigns,
    ];
    return view('main-dashboard',$data);
   }
}
