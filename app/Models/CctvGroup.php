<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CctvGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function cctvs()
    {
        return $this->hasMany(Cctv::class);
    }
}
