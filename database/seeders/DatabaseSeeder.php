<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cctv;
use App\Models\Menu;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Permissions
        $permissions = [
            'manage cctvs',
            'manage users',
            'manage roles',
            'manage menus',
            'view dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // 2. Seed Roles
        $adminRole = Role::findOrCreate('admin');
        $operatorRole = Role::findOrCreate('operator');
        $viewerRole = Role::findOrCreate('viewer');

        // Assign Permissions to Roles
        $adminRole->syncPermissions(Permission::all());
        $operatorRole->syncPermissions(['manage cctvs', 'view dashboard']);
        $viewerRole->syncPermissions(['view dashboard']);

        // 3. Create Users
        $adminUser = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@cctv.local',
            'password' => Hash::make('password'),
        ]);
        $adminUser->assignRole($adminRole);

        $operatorUser = User::create([
            'name' => 'Operator CCTV',
            'email' => 'operator@cctv.local',
            'password' => Hash::make('password'),
        ]);
        $operatorUser->assignRole($operatorRole);

        $viewerUser = User::create([
            'name' => 'Viewer Publik',
            'email' => 'viewer@cctv.local',
            'password' => Hash::make('password'),
        ]);
        $viewerUser->assignRole($viewerRole);

        // 4. Seed Dynamic Menus
        $menus = [
            [
                'name' => 'Dashboard',
                'icon' => 'layout-dashboard',
                'url' => '/dashboard',
                'order' => 1,
                'permission_name' => 'view dashboard',
            ],
            [
                'name' => 'Master CCTV',
                'icon' => 'video',
                'url' => '/admin/cctvs',
                'order' => 2,
                'permission_name' => 'manage cctvs',
            ],
            [
                'name' => 'User Management',
                'icon' => 'users',
                'url' => '/admin/users',
                'order' => 3,
                'permission_name' => 'manage users',
            ],
            [
                'name' => 'Role & Permission',
                'icon' => 'shield-check',
                'url' => '/admin/roles',
                'order' => 4,
                'permission_name' => 'manage roles',
            ],
            [
                'name' => 'Menu Management',
                'icon' => 'menu',
                'url' => '/admin/menus',
                'order' => 5,
                'permission_name' => 'manage menus',
            ],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }

        // 5. Seed CCTVs
        $cctvs = [
            [
                'name' => 'Bundaran HI - Jakarta',
                'ip' => '192.168.1.10',
                'port' => 8000,
                'channel' => 1,
                'username' => 'admin',
                'password' => 'admin123',
                'stream_url' => 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8',
                'latitude' => -6.19500000,
                'longitude' => 106.82300000,
                'status' => 'active',
            ],
            [
                'name' => 'Monumen Nasional (Monas) - Jakarta',
                'ip' => '192.168.1.11',
                'port' => 8000,
                'channel' => 1,
                'username' => 'admin',
                'password' => 'admin123',
                'stream_url' => 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8',
                'latitude' => -6.17540000,
                'longitude' => 106.82720000,
                'status' => 'active',
            ],
            [
                'name' => 'Gedung Sate - Bandung',
                'ip' => '192.168.1.12',
                'port' => 8080,
                'channel' => 1,
                'username' => 'admin',
                'password' => 'admin123',
                'stream_url' => 'https://playertest.longtailvideo.com/adaptive/bipbop/bipbop.m3u8',
                'latitude' => -6.90250000,
                'longitude' => 107.61860000,
                'status' => 'active',
            ],
            [
                'name' => 'Tugu Yogyakarta - DIY',
                'ip' => '192.168.1.13',
                'port' => 554,
                'channel' => 2,
                'username' => 'operator',
                'password' => 'op12345',
                'stream_url' => 'https://cph-p2p-msl.akamaized.net/hls/live/2000341/test/master.m3u8',
                'latitude' => -7.78290000,
                'longitude' => 110.36710000,
                'status' => 'inactive',
            ],
        ];

        foreach ($cctvs as $cctv) {
            Cctv::create($cctv);
        }
    }
}
