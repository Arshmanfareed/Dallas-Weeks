<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seats extends Model
{
    use HasFactory;

    protected $table = 'seats';

    protected $fillable = [
        'creator_id',
        'team_id',
        'company_info_id',
        'payment_id',
        'seat_info_id',
        'integration_id',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function team()
    {
        return $this->belongsTo(Teams::class, 'team_id');
    }

    public function company()
    {
        return $this->belongsTo(CompanyInfo::class, 'company_info_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function seatInfo()
    {
        return $this->belongsTo(SeatInfo::class, 'seat_info_id');
    }
}
