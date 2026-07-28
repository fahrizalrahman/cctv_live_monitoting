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
        'cctv_group_id',
    ];

    public function group()
    {
        return $this->belongsTo(CctvGroup::class, 'cctv_group_id');
    }
}
