<?php

namespace Database\Factories;

use App\Models\MidiFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MidiFile>
 */
class MidiFileFactory extends Factory
{
    protected $model = MidiFile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ucfirst($this->faker->unique()->words(3, true)),
            'video_path' => $this->faker->randomElement([
                'dQw4w9WgXcQ',
                '3JZ_D3ELwOQ',
                'kXYiU_JCYtU',
                'M7lc1UVf-VE',
            ]),
            'video_type' => 'youtube',
            'midi_file_path' => null,
            'lmv_file_path' => null,
            'thumbnail_path' => $this->faker->optional()->randomElement([
                'images/featured1.jpeg',
                'images/featured2.jpeg',
                'images/featured3.jpeg',
                'images/services2.jpeg',
                'images/services4.jpeg',
                'images/pro.jpeg',
            ]),
            'description' => $this->faker->sentence(),
        ];
    }
}
