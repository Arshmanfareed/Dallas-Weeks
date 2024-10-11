<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Global_Blacklist extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     */
    protected $table = 'global_blacklist';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'creator_id',
        'team_id',
        'keyword',
        'blacklist_type',
        'comparison_type',
    ];
}
