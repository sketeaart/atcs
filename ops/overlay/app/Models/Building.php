<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;

    protected $fillable = ['name','lat','lng','icon'];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function cctvs()
    {
        return $this->hasMany(Cctv::class);
    }
}

