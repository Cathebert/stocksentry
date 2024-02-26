<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
class InventoryPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
          Permission::create(['name' => 'add lab']);
        Permission::create(['name' => 'edit lab']);
        Permission::create(['name' => 'add section']);
        Permission::create(['name' => 'edit section']);
        Permission::create(['name' => 'add section user']);
        Permission::create(['name' => 'edit section user']);
        Permission::create(['name' => 'remove section user']);
        Permission::create(['name' => 'add user']);
        Permission::create(['name' => 'edit user']);
        Permission::create(['name' => 'delete user']);
        Permission::create(['name' => 'add supplier']);
        Permission::create(['name' => 'edit supplier']);
        Permission::create(['name' => 'remove supplier']);
        Permission::create(['name' => 'add item']);
        Permission::create(['name' => 'edit item']);
        Permission::create(['name' => 'adjust stock']);
        Permission::create(['name' => 'approve stock']);
        Permission::create(['name' => 'transfer stock']);
        Permission::create(['name' => 'view inventory']);
       Permission::create(['name' => 'view inventory section']);

        $role1 = Role::create(['name' => 'admin']);

        $role2 = Role::create(['name' => 'moderator']);
        $role2->givePermissionTo('add section');
        $role2->givePermissionTo('edit section');
        $role2->givePermissionTo('add section user');
        $role2->givePermissionTo('edit section user');
        $role2->givePermissionTo('remove section user');
        $role2->givePermissionTo('add supplier');
        $role2->givePermissionTo('edit supplier');
        $role2->givePermissionTo('add item');
        $role2->givePermissionTo('edit item');
        $role2->givePermissionTo('adjust stock');
        $role2->givePermissionTo('approve stock');
        $role2->givePermissionTo('transfer stock');

        $role3=Role::create(['name'=>'technician']);
        $role3->givePermissionTo('add item');
        $role3->givePermissionTo('adjust stock');
         $role3->givePermissionTo('view inventory section');

           $user = \App\Models\User::factory()->create([
            'name' => 'Cat',
            'authority'=>1,
            'last_name'=>'Muyila',
            'email' => 'cdmuyila@gmail.com',
        ]);
        $user->assignRole($role1);

        
 $user = \App\Models\User::factory()->create([
            'name' => 'Carol',
            'authority'=>2,
            'last_name'=>'Chirwa',
            'email' => 'cmincorp2019@gmail.com',
        ]);
        $user->assignRole($role2);

$user = \App\Models\User::factory()->create([
            'name' => 'testU',
            'authority'=>3,
            'last_name'=>'Test',
            'email' => 'user@gmail.com',
        ]);
        $user->assignRole($role3);


    }
}
