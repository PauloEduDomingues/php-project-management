<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'admin'  
        ])->syncPermissions([
            'project.create',
            'project.delete',
            'project.update',
            'task.create',
            'task.update',
            'task.delete',
            'project.read',
            'task.read',
            'task.status'
        ]);

        Role::create([
            'name' => 'manager'  
        ])->syncPermissions([
            'project.update',
            'task.create',
            'task.update',
            'task.delete',
            'project.read',
            'task.read',
            'task.status'
        ]);

        Role::create([
            'name' => 'colaborator'
        ])->syncPermissions([
            'project.read',
            'task.read',
            'task.status'
        ]);
    }
}
