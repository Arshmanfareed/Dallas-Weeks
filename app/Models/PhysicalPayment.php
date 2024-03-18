<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicalPayment extends Model
{
    use HasFactory;
    protected $table = 'physical_payment';
    protected $guarded = [];
}
