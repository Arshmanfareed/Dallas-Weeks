<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page.
     * 
     * This function sets the title for the home page and renders the 'home' view
     * with the provided data.
     *
     * @return \Illuminate\View\View
     */
    public function home()
    {
        /* Set the title for the home page */
        $data = ['title' => 'Networked'];

        /* Render the 'home' view with the provided data */
        return view('home', $data);
    }

    /**
     * Display the about page.
     * 
     * This function sets the title for the about page and renders the 'about' view
     * with the provided data.
     *
     * @return \Illuminate\View\View
     */
    public function about()
    {
        /* Set the title for the about page */
        $data = ['title' => 'Networked'];

        /* Render the 'about' view with the provided data */
        return view('about', $data);
    }

    /**
     * Display the pricing page.
     * 
     * This function sets the title for the pricing page and renders the 'pricing' view
     * with the provided data.
     *
     * @return \Illuminate\View\View
     */
    public function pricing()
    {
        /* Set the title for the pricing page */
        $data = ['title' => 'Networked'];

        /* Render the 'pricing' view with the provided data */
        return view('pricing', $data);
    }

    /**
     * Display the FAQ page.
     * 
     * This function sets the title for the FAQ page and renders the 'faq' view
     * with the provided data.
     *
     * @return \Illuminate\View\View
     */
    public function faq()
    {
        /* Define the data to be passed to the FAQ view */
        $data = ['title' => 'Networked'];

        /* Render the 'faq' view with the title data */
        return view('faq', $data);
    }
}
