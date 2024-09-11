<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'item_name',
        'item_price',
        'item_price_currency',
        'transaction_id',
        'status',
        'paid_amount',
        'paid_amount_currency',
        'uploaded_month',
    ];
}
