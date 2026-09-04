<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'book_id' => Book::factory(),
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'rate' => fake()->numberBetween(1, 5),
            'comment' => fake()->realText(100),
        ];
    }
}
