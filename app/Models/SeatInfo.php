<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatInfo extends Model
{
    use HasFactory;
    protected $table = 'seat_info';
    protected $guarded = [];
}