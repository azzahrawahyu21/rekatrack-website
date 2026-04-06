<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'users';

    protected $fillable = [
        'nip', 'role_id', 'name', 'email', 'password', 'phone_number', 'avatar', // 👈 tambahkan 'avatar'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ✅ Tambahkan accessor untuk avatar dengan fallback
    // public function getAvatarUrlAttribute()
    // {
    //     // Cek jika avatar ada di public/images/user/
    //     if ($this->avatar && file_exists(public_path($this->avatar))) {
    //         return asset($this->avatar); // → asset('images/user/avatar_1_123.jpg')
    //     }

    //     // Fallback
    //     return asset('images/user/default-avatar.jpg');
    // }
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset($this->avatar);
        }
        return asset('images/user/default-avatar.jpg');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function tracks()
    {
        return $this->hasMany(Track::class, 'driver_id');
    }

    public function hasAnyRelationship()
    {
        if ($this->tracks()->exists()) {
            return true;
        }
        return false;
    }
}
