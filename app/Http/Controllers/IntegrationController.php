<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IntegrationController extends Controller
{
   function integration(){
    $data=[
        'title'=>'Integration'
    ];
    return view('integrations',$data);
   }
}
