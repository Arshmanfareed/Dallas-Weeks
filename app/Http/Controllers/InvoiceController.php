<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    function invoice()
    {
        try {
            $data = [
                'title' => 'Invoices'
            ];
            return view('invoice', $data);
        } catch (Exception $e) {
            return redirect('login')->withErrors(['error' => $e->getMessage()]);
        }
    }
}
