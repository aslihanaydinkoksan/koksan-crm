<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Person;

class PersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $people = [
            ['first_name' => 'Ahmet', 'last_name' => 'Yılmaz', 'email' => 'ahmet@test.com', 'phone' => '05551112233'],
            ['first_name' => 'Ayşe', 'last_name' => 'Demir', 'email' => 'ayse@test.com', 'phone' => '05324445566'],
        ];

        foreach ($people as $person) {
            Person::updateOrCreate(['email' => $person['email']], $person);
        }
    }
}
