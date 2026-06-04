<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Location;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Lokasyonlar
        $locations = [
            ['name' => 'Merkez', 'slug' => 'merkez', 'type' => 'Ofis'],
            ['name' => 'Hendek', 'slug' => 'hendek', 'type' => 'Fabrika'],
        ];

        foreach ($locations as $loc) {
            Location::updateOrCreate(['slug' => $loc['slug']], $loc);
        }

        // 2. Departmanlar
        $departments = [
            ['name' => 'Yönetim Kurulu', 'slug' => 'yonetim-kurulu'],
            ['name' => 'İdari İşler', 'slug' => 'idari-isler'],
            ['name' => 'Kopet İş Birimi', 'slug' => 'kopet-is-birimi'],
        ];

        foreach ($departments as $dep) {
            Department::updateOrCreate(['slug' => $dep['slug']], $dep);
        }

        // 3. Çekirdek Roller (Sistem Rolleri - is_system = true)
        $roles = [
            ['name' => 'Süper Admin', 'slug' => 'super-admin', 'hierarchy_weight' => 90, 'is_system' => true],
            ['name' => 'Direktör', 'slug' => 'direktor', 'hierarchy_weight' => 80, 'is_system' => true],
            ['name' => 'Kıdemli Müdür', 'slug' => 'kidemli-mudur', 'hierarchy_weight' => 70, 'is_system' => true],
            ['name' => 'Müdür', 'slug' => 'mudur', 'hierarchy_weight' => 60, 'is_system' => true],
            ['name' => 'Departman Yöneticisi', 'slug' => 'departman-yoneticisi', 'hierarchy_weight' => 50, 'is_system' => true],
            ['name' => 'Standart Kullanıcı', 'slug' => 'standart-kullanici', 'hierarchy_weight' => 10, 'is_system' => true],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(['slug' => $roleData['slug']], $roleData);
        }

        // 4. İlk Kullanıcılar
        $merkezLocation = Location::where('slug', 'merkez')->first();
        $yonetimDepartment = Department::where('slug', 'yonetim-kurulu')->first();

        // Developer (God Mode)
        User::updateOrCreate(
            ['email' => 'developer@koksan.com'],
            [
                'name' => 'Developer God Mode',
                'password' => Hash::make('password123'), // Prod ortamında bu değiştirilmeli
                'location_id' => $merkezLocation->id,
                'department_id' => $yonetimDepartment->id,
                'is_developer' => true,
            ]
        );

        // Süper Admin (Standart Gate::before'dan geçemeyen ama tüm rolleri kapsayacak kişi)
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@koksan.com'],
            [
                'name' => 'Sistem Yöneticisi',
                'password' => Hash::make('password123'),
                'location_id' => $merkezLocation->id,
                'department_id' => $yonetimDepartment->id,
                'is_developer' => false,
            ]
        );

        // Süper Admin Rolünü Ata
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        if ($superAdminRole && !$superAdmin->hasRole('super-admin')) {
            $superAdmin->roles()->syncWithoutDetaching([$superAdminRole->id]);
        }
        $this->call([
            CustomerDynamicFieldSeeder::class,
            // İleride yazacağımız Lojistik, İhracat vb. seeder'ları da buraya alt alta ekleyeceğiz.
            PersonSeeder::class,
            SampleDynamicFieldSeeder::class
        ]);
    }
}
