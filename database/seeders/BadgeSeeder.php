<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Badge::updateOrCreate(
            ['badge_code' => 'first_submission'],
            [
                'name' => 'First Blood',
                'description' => 'Matagumpay na nagpasa ng iyong kauna-unahang assignment.',
                'icon_class' => 'fa-graduation-cap text-blue-500'
            ]
        );

        Badge::updateOrCreate(
            ['badge_code' => 'streak_3'],
            [
                'name' => 'Loyal Scholar',
                'description' => 'Napanatili ang active study streak sa loob ng 3 araw.',
                'icon_class' => 'fa-fire text-orange-500'
            ]
        );
    }
}
