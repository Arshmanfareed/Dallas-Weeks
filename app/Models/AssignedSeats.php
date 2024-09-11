<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedSeats extends Model
{
    use HasFactory;

    protected $table = 'assigned_seats';

    protected $fillable = [
        'member_id',
        'role_id',
        'seat_id',
    ];

    public function member()
    {
        return $this->belongsTo(TeamMembers::class, 'member_id');
    }

    public function role()
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }

    public function seat()
    {
        return $this->belongsTo(Seats::class, 'seat_id');
    }
}
