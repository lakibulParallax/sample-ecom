<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $email = 'admin@admin.com';
        $admin = Admin::where('email', $email)->first();
        if (!$admin) {
            $admin = new Admin();
            $admin->name = 'admin';
            $admin->email = 'admin@admin.com';
            $admin->password = Hash::make(12345678);
            $admin->save();
        }
    }
}
