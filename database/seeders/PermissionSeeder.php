<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create([
            'name' => 'project.read'
        ]);
        Permission::create([
            'name' => 'project.create'
        ]);
        Permission::create([
            'name' => 'project.update'
        ]);
        Permission::create([
            'name' => 'project.delete' 
        ]);
        Permission::create([
            'name' => 'task.read'
        ]);
        Permission::create([
            'name' => 'task.create'
        ]);
        Permission::create([
            'name' => 'task.update'
        ]);
        Permission::create([
            'name' => 'task.delete'
        ]);
        Permission::create([
            'name' => 'task.status'
        ]);
    }
}
