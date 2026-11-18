<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flora extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'season', 'icon', 'description'];
    public function regions() { return $this->belongsToMany(Region::class); }
}
