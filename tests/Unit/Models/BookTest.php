<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class BookTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_belongs_to_a_user(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(BelongsTo::class, $book->user());
        $this->assertTrue($book->user->is($user));
    }

    public function test_book_can_have_multiple_genres(): void
    {
        $book = Book::factory()->create();
        $genres = Genre::factory()->count(3)->create();
        $book->genres()->attach($genres);

        $this->assertInstanceOf(BelongsToMany::class, $book->genres());
        $this->assertCount(3, $book->genres);
    }

    public function test_book_has_many_reviews(): void
    {
        $book = Book::factory()->create();
        Review::factory()->count(2)->create(['book_id' => $book->id]);

        $this->assertInstanceOf(HasMany::class, $book->reviews());
        $this->assertCount(2, $book->reviews);
    }

    public function test_book_can_be_favorited_by_multiple_users(): void
    {
        $book = Book::factory()->create();
        $users = User::factory()->count(2)->create();
        $book->favoritedBy()->attach($users);

        $this->assertInstanceOf(BelongsToMany::class, $book->favoritedBy());
        $this->assertCount(2, $book->favoritedBy);
    }

    public function test_isbn_is_unique_in_the_database(): void
    {
        Book::factory()->create(['isbn' => '1234567890123']);

        $this->expectException(QueryException::class);

        Book::factory()->create(['isbn' => '1234567890123']);
    }

    public function test_deleting_a_book_also_deletes_related_reviews_genre_links_and_favorites(): void
    {
        $book = Book::factory()->create();

        $genre = Genre::factory()->create();
        $book->genres()->attach($genre);

        $favoriter = User::factory()->create();
        $book->favoritedBy()->attach($favoriter);

        $review = Review::factory()->create(['book_id' => $book->id]);

        $book->delete();

        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
        $this->assertDatabaseMissing('book_genre', ['book_id' => $book->id]);
        $this->assertDatabaseMissing('user_book', ['book_id' => $book->id]);
    }
}
