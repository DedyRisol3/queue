<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'queue_number', 
        'session', 
        'status', 
        'user_phone'
    ];
}