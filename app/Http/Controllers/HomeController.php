<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        /* Set the title for the home page */
        $data = ['title' => 'Networked'];

        /* Render the 'home' view with the provided data */
        return view('home', $data);
    }

    public function about()
    {
        /* Set the title for the about page */
        $data = ['title' => 'Networked'];

        /* Render the 'about' view with the provided data */
        return view('about', $data);
    }

    public function pricing()
    {
        /* Set the title for the pricing page */
        $data = ['title' => 'Networked'];

        /* Render the 'pricing' view with the provided data */
        return view('pricing', $data);
    }

    public function faq()
    {
        /* Define the data to be passed to the FAQ view */
        $data = ['title' => 'Networked'];

        /* Render the 'faq' view with the title data */
        return view('faq', $data);
    }
}
