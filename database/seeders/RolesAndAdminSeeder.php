<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаём роли для обычных пользователей
        $sellerRole = Role::firstOrCreate(['name' => 'seller']);
        $buyerRole = Role::firstOrCreate(['name' => 'buyer']);

        // Выдаем права для продавца
        $sellerPermissions = [
            'view_shop', 'view_any_shop', 'create_shop', 'update_shop', 'delete_shop',
            'view_product', 'view_any_product', 'create_product', 'update_product', 'delete_product',
            'view_response', 'view_any_response', 'create_response', 'update_response',
            'view_review', 'view_any_review', // Продавцы могут только просматривать отзывы
            'view_address', 'view_any_address', 'create_address', 'update_address', 'delete_address',
            'view_users::info', 'view_any_users::info', 'create_users::info', 'update_users::info', 'delete_users::info',
            'view_any_customer::request', // Продавцы могут видеть все заявки
        ];
        foreach ($sellerPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $sellerRole->syncPermissions($sellerPermissions);

        // Админ. Он получает роль 'super_admin', которая создается в ShieldSeeder.
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'avatar' => 'https://ui-avatars.com/api/?name=Admin',
            ]
        );
        $admin->assignRole('super_admin');

        // Продавец
        $seller = User::firstOrCreate(
            ['email' => 'seller@site.com'],
            [
                'name' => 'Seller',
                'password' => bcrypt('password'),
                'avatar' => 'https://ui-avatars.com/api/?name=Seller',
            ]
        );
        $seller->assignRole($sellerRole);

        // Покупатель
        $buyer = User::firstOrCreate(
            ['email' => 'buyer@site.com'],
            [
                'name' => 'Buyer',
                'password' => bcrypt('password'),
                'avatar' => 'https://ui-avatars.com/api/?name=Buyer',
            ]
        );
        $buyer->assignRole($buyerRole);
    }
}
