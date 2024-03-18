<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    function report(){
        $data=[
            'title'=>'Report'
        ];
        return view('reports',$data);

    }
}
