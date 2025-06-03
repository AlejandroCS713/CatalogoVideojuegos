<?php

namespace App\Models\users;

use App\Models\Foro\Foro;
use App\Models\Foro\MensajeForo;
use App\Models\Foro\RespuestaForo;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\users\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',

    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function friends() {
        return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id')
            ->where('status', 'accepted');
    }

    public function friendRequests() {
        return $this->hasMany(Friend::class, 'friend_id')->where('status', 'pending');
    }

    public function logros() {
        return $this->belongsToMany(Logro::class, 'logro_usuario')->withTimestamps();
    }

    public function foros()
    {
        return $this->hasMany(Foro::class, 'usuario_id');
    }

    public function mensajesForo()
    {
        return $this->hasMany(MensajeForo::class, 'usuario_id');
    }

    public function respuestasForo()
    {
        return $this->hasMany(RespuestaForo::class, 'usuario_id');
    }

}
