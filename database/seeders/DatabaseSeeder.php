<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed data awal untuk development.
     */
    public function run(): void
    {
        // Buat akun Owner default
        User::create([
            'username' => 'owner',
            'email'    => 'owner@tokolistrik.com',
            'password' => 'password', // Otomatis di-hash oleh model cast
            'role'     => 'owner',
        ]);

        // Buat akun Admin default
        User::create([
            'username' => 'admin',
            'email'    => 'admin@tokolistrik.com',
            'password' => 'password',
            'role'     => 'admin',
        ]);

        // Buat akun Kasir default
        User::create([
            'username' => 'kasir',
            'email'    => 'kasir@tokolistrik.com',
            'password' => 'password',
            'role'     => 'kasir',
        ]);
    }
}
