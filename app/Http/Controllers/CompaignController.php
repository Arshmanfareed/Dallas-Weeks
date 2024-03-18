<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompaignController extends Controller
{
    function compaign(){
        $data=[
            'title'=>'Compaign'
        ];
        return view('compaign',$data);
    }
    function compaigncreate(){
        $data=[
            'title'=>'Create Compaign'
        ];
        return view('compaigncreate',$data);
    }
    function compaigninfo(){
        $data=[
            'title'=>'Create Compaign Info'
        ];
        return view('createcompaigninfo',$data);
    }
}
