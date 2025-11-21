<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChatRoom;

class ChatRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            [
                'name' => 'General Premium Chat',
                'is_premium' => true,
            ],
            [
                'name' => 'Business Discussions',
                'is_premium' => true,
            ],
            [
                'name' => 'Casual Lounge',
                'is_premium' => true,
            ],
            [
                'name' => 'Announcements',
                'is_premium' => true,
            ],
        ];

        foreach ($rooms as $room) {
            ChatRoom::updateOrCreate(
                ['name' => $room['name']],
                ['is_premium' => $room['is_premium']]
            );
        }
    }
}
