<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kantor extends Model
{
    protected $fillable = [
        'kode_kantor',
        'nama_kantor',
    ];
    protected $table = 'kantor';
    protected $primaryKey = 'id';

    // 🔗 Relasi ke User (1 kantor punya banyak user)
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
