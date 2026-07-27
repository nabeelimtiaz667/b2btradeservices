<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'name'       => 'Admin',
            'email'      => 'admin@b2btrade.com',
            'password'   => password_hash('admin123', PASSWORD_DEFAULT),
            'phone'      => '1234567890',
            'user_type'  => 'admin',
            'status'     => 'approved',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->table('users')->insert($data);
    }
}
