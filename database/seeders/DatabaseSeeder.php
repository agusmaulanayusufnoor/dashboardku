<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Divisi;
use App\Models\Kantor;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\ModelHasRoleSeeder;
use Database\Seeders\RoleHasPermissionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Kantor::create([
            'kode_kantor' => '001',
            'nama_kantor' => 'PUSAT'
        ]);

        Divisi::create([
            'nama_divisi' => 'IT'
        ]);
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'test@example.com',
            'username' => 'admin',
            'password' => bcrypt('12345678'), // password
            'kantor_id' => 1,
            'divisi_id' => 1,
        ]);

        $this->call([
            PermissionSeeder::class,
        ]);

        $this->call(RoleSeeder::class);
        $this->call(ModelHasRoleSeeder::class);
        $this->call(RoleHasPermissionSeeder::class);
    }
}
