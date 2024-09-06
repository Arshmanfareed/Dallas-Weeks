<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedSeats extends Model
{
    use HasFactory;
    protected $table = 'assigned_seats';
    protected $guarded = [];
}
