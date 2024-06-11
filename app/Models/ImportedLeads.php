<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportedLeads extends Model
{
    use HasFactory;
    protected $table = 'imported_leads';
    protected $guarded = '';
}
