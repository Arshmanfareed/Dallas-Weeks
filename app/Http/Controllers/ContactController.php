<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    function contact(){
        $data=[
            'title'=>'Contacts'
        ];
        return view('contact',$data);
    }
}
