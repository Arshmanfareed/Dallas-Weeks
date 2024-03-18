<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MessageController extends Controller
{
    function message(){
        $data=[
            'title'=>'Message'
        ];
        return view('message',$data);
    }
}
