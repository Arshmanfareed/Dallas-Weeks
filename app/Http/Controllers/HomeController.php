<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $data = ['title' => 'Networked'];

    public function home()
    {
        return view('home', $this->data);
    }

    public function about()
    {
        return view('about', $this->data);
    }

    public function pricing()
    {
        return view('pricing', $this->data);
    }

    public function faq()
    {
        return view('faq', $this->data);
    }
}
