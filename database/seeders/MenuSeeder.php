<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuItem;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ads = [            
            ['id' => 204, 'parent_key' => NULL, 'order' => 204, 'key' => 'voice_chatbot', 'route' => 'user.extension.voice.chatbot', 'route_slug' => NULL, 'label' => 'Voice Chatbot', 'icon' => 'fa-solid fa-message-bot', 'type' => 'item', 'svg' => NULL, 'is_active' => 1, 'is_admin' => 0, 'extension' => 1, 'url' => NULL, 'permission' => NULL, 'conditions' => [], 'badge_text' => NULL, 'badge_type' => NULL, 'children' => [], 'original' => 1],
            ['id' => 205, 'parent_key' => NULL, 'order' => 205, 'key' => 'human_agent', 'route' => 'user.extension.human.agent', 'route_slug' => NULL, 'label' => 'Human Agent', 'icon' => 'fa-solid fa-headset', 'type' => 'item', 'svg' => NULL, 'is_active' => 1, 'is_admin' => 0, 'extension' => 1, 'url' => NULL, 'permission' => NULL, 'conditions' => [], 'badge_text' => NULL, 'badge_type' => NULL, 'children' => [], 'original' => 1],
        ];  

        foreach ($ads as $ad) {
            MenuItem::updateOrCreate(['id' => $ad['id']], $ad);
        }
    }
}
