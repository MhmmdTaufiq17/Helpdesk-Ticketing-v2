<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@helpdesk.com',
            'password' => bcrypt('admin123'),
            'role' => 'super_admin'
        ]);

        // Create sample categories
        $categories = [
            'Login Issues',
            'Software Installation',
            'Network Problems',
            'Hardware Issues',
            'Email Configuration',
            'Database Errors',
            'Performance Issues',
            'Security Concerns',
            'Website Problems',
            'Mobile App Issues'
        ];

        foreach ($categories as $category) {
            Category::create(['category_name' => $category]);
        }
    }
}
