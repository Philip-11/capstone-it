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
                'description' => 'You submitted your very first assignment!',
                'icon_class' => 'fa-graduation-cap text-blue-500'
            ]
        );

        Badge::updateOrCreate(
            ['badge_code' => 'streak_3'],
            [
                'name' => 'Loyal Scholar',
                'description' => 'You have logged in for 3 straight days.',
                'icon_class' => 'fa-fire text-orange-500'
            ]
        );
    }
}
