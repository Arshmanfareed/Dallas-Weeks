<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    function invoice()
    {
        $data = [
            'title' => 'Invoices'
        ];
        return view('invoice', $data);
    }
}
