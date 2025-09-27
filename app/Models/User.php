<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * Kolom yang bisa diisi mass-assignment
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'kantor_id',
        'divisi_id',
    ];

    /**
     * Kolom yang disembunyikan saat serialisasi
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting kolom
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

    // 🔗 Relasi ke Kantor
    public function kantor()
    {
        return $this->belongsTo(Kantor::class);
    }

    // 🔗 Relasi ke Divisi
    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }
}
