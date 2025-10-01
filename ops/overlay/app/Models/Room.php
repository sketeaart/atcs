<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['building_id','name','icon'];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function cctvs()
    {
        return $this->hasMany(Cctv::class);
    }
}

