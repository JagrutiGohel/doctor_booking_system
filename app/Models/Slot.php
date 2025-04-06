<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    //
    protected $fillable = ['type', 'day', 'from', 'to', 'date', 'booked_count'];
}
