<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    protected $table = 'divisi';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_divisi',
    ];

    // 🔗 Relasi ke User (1 divisi punya banyak user)
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
