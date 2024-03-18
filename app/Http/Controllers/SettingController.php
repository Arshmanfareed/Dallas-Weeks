<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
   function settingrolespermission(){
    $data=[
        'title'=>'Setting'
    ];
    return view('setting',$data);
   }
   function setting(){
    $data=[
        'title'=>'Setting'
    ];
    return view('settings',$data);
}
}
