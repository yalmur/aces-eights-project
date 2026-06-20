<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartyHallInquiry extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'event_date', 'guests', 'event_type', 'message', 'status',
    ];

    protected $casts = [
        'event_date' => 'date',
        'guests'     => 'integer',
    ];
}
