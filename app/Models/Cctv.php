<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cctv extends Model
{
    protected $fillable = [
        'name',
        'ip',
        'port',
        'channel',
        'username',
        'password',
        'stream_url',
        'latitude',
        'longitude',
        'status',
    ];
}
