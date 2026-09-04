<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_review_belongs_to_a_book(): void
    {
        $book = Book::factory()->create();
        $review = Review::factory()->create(['book_id' => $book->id]);

        $this->assertInstanceOf(BelongsTo::class, $review->book());
        $this->assertTrue($review->book->is($book));
    }

    public function test_review_belongs_to_a_user(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(BelongsTo::class, $review->user());
        $this->assertTrue($review->user->is($user));
    }

    public function test_review_can_be_liked_by_multiple_users(): void
    {
        $review = Review::factory()->create();
        $likers = User::factory()->count(2)->create();
        $review->likedBy()->attach($likers);

        $this->assertInstanceOf(BelongsToMany::class, $review->likedBy());
        $this->assertCount(2, $review->likedBy);
    }

    public function test_rate_is_treated_as_an_integer(): void
    {
        $review = Review::factory()->create(['rate' => 4]);

        $this->assertIsInt($review->rate);
        $this->assertSame(4, $review->rate);
    }

    public function test_deleting_a_review_also_deletes_related_likes(): void
    {
        $review = Review::factory()->create();
        $liker = User::factory()->create();
        $review->likedBy()->attach($liker);

        $review->delete();

        $this->assertDatabaseMissing('user_review', ['review_id' => $review->id]);
    }
}
