<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    function home(){
        $data=[
            'title'=>'Networked'
        ];
        
        return view('home',$data);
    }
    function about(){
        $data=[
            'title'=>'Networked'
        ];
        return view('about',$data);
    }
    function pricing(){
        $data=[
            'title'=>'Networked'
        ];
        return view('pricing',$data);
    }
    function faq(){
        $data=[
            'title'=>'Networked'
        ];
        return view('faq',$data);
    }
}