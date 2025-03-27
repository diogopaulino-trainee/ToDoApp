<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model {
    use HasFactory;
    protected $fillable = ['name', 'description', 'required_tasks'];

    public function users() {
        return $this->belongsToMany(User::class, 'user_levels')
                    ->withPivot('animation_seen')
                    ->withTimestamps();
    }
}
