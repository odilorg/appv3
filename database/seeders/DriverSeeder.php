<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Driver;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = [
            [
                'name' => 'Hasan Karimov',
                'email' => 'hasan.karimov@example.com',
                'is_active' => true,
                'notes' => 'Experienced sedan driver, fluent in English',
                'address' => 'Samarkand, Uzbekistan',
                'phone01' => '+998901234567',
                'phone02' => '+998911234567',
                'image' => 'drivers/hasan.jpg',
                'license_number' => 'UZB123456',
                'license_expires_at' => Carbon::now()->addYears(2),
                'license_image' => 'licenses/hasan_license.jpg',
            ],
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'is_active' => true,
                'notes' => 'Minibus specialist, 10 years of experience',
                'address' => 'Bukhara, Uzbekistan',
                'phone01' => '+998931234567',
                'phone02' => null,
                'image' => 'drivers/john.jpg',
                'license_number' => 'UZB654321',
                'license_expires_at' => Carbon::now()->addYears(3),
                'license_image' => 'licenses/john_license.jpg',
            ],
            [
                'name' => 'Ali Usmonov',
                'email' => 'ali.usmonov@example.com',
                'is_active' => false,
                'notes' => 'Currently inactive, seasonal driver',
                'address' => 'Tashkent, Uzbekistan',
                'phone01' => '+998941234567',
                'phone02' => '+998951234567',
                'image' => 'drivers/ali.jpg',
                'license_number' => 'UZB777888',
                'license_expires_at' => Carbon::now()->addYear(),
                'license_image' => 'licenses/ali_license.jpg',
            ],
        ];

        foreach ($drivers as $data) {
            Driver::updateOrCreate(
                ['email' => $data['email']], // unique check
                $data
            );
        }
    }
}
