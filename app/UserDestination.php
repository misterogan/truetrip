<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDestination extends Model
{
    protected $fillable = [
        'user_id','title', 'origin', 'destination', 'type','start','end','description','created_at','updated_at'
    ];
}
