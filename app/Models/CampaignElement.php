<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignElement extends Model
{
    use HasFactory;
    protected $table = 'elements';
    protected $guarded = '';
}
