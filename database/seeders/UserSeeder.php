<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        User::create([
            'name' => 'Admin',
            'email' => 'admin@teste.com',
            'password' => '123456' // Será criptografada automaticamente pelo mutator
        ]);

        User::create([
            'name' => 'João Silva',
            'email' => 'joao@teste.com',
            'password' => 'senha123'
        ]);

    }
}
