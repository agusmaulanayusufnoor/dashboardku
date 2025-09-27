<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleHasPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [];

        for ($i = 1; $i <= 20; $i++) {
            $permissions[] = [
                'permission_id' => $i,
                'role_id' => 1,
            ];
        }

        DB::table('role_has_permissions')->insert($permissions);
    }
}
