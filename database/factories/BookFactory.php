<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(3);
        return [
            'title' => $title,
            'isbn' => $this->faker->unique()->isbn13(),
            'description' => $this->faker->text(150),
            'author_id' => Author::inRandomOrder()->first()?->id,
            'genre' => $this->faker->randomElement([
                'Science Fiction',
                'Fantasy',
                'Romance',
                'Thriller',
                'Mystery',
                'Non-fiction',
                'Historical',
            ]),
            'published_at' => $this->faker->date(),
            'total_copies' => $this->faker->numberBetween(5, 100),
            'cover_image' => "https://placehold.co/200x300/#F3F4F6/FFFFFF?text=" . urlencode($title),
            'available_copies' => $this->faker->numberBetween(1, 100),
            'price' => $this->faker->randomFloat(2, 5, 50),
        ];
    }
}
