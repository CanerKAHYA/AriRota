<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'province', 'latitude', 'longitude', 'elevation', 'capacity', 'description', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function floras() { return $this->belongsToMany(Flora::class); }
    public function hives() { return $this->hasMany(Hive::class); }
    public function alerts() { return $this->hasMany(Alert::class); }
    public function occupiedCount(): int { return (int) $this->hives()->where('status', 'active')->sum('hive_count'); }
    public function availableCapacity(): int { return max(0, $this->capacity - $this->occupiedCount()); }
}
