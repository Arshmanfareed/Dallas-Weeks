<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyInfo extends Model
{
    use HasFactory;

    protected $table = 'company_info';

    protected $fillable = [
        'street_address',
        'city',
        'state',
        'postal_code',
        'country',
        'company_name',
        'tax_id',
    ];
}
