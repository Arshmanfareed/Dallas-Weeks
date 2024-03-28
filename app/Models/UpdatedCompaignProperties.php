<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpdatedCompaignProperties extends Model
{
    use HasFactory;
    protected $table = 'compaign_properties';
    protected $guarded = [];
}
