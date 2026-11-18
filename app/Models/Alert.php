<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;
    protected $fillable = ['region_id', 'user_id', 'type', 'severity', 'title', 'message', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
    public function region() { return $this->belongsTo(Region::class); }
    public function user() { return $this->belongsTo(User::class); }
}
