<?php

namespace App\Models\users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'friend_id', 'status'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function friend() {
        return $this->belongsTo(User::class, 'friend_id');
    }
}
