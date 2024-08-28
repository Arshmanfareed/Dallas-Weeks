<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            Log::info($e);
            return redirect('login')->withErrors(['error' => $e->getMessage()]);
        }
    }
}
