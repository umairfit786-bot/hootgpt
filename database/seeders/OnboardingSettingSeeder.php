<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OnboardingSetting;

class OnboardingSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'id' => 1,
            'welcome_title' => 'Welcome to DaVinci AI!',
            'welcome_message' => 'Let us guide you through the amazing features of DaVinci AI.',
            'welcome_icon' => '',
            'welcome_banner' => null,
            'completion_title' => 'Tour Successfully Completed!',
            'completion_message' => 'You are now ready to explore all the powerful AI tools at your disposal.',
            'completion_icon' => '',
            'completion_banner' => null
        ];

        OnboardingSetting::updateOrCreate(['id' => $settings['id']], $settings);
    }
}