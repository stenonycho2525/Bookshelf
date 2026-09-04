<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use RefreshDatabase;

    public function test_genre_can_have_multiple_books(): void
    {
        $genre = Genre::factory()->create();
        $books = Book::factory()->count(2)->create();
        $genre->books()->attach($books);

        $this->assertInstanceOf(BelongsToMany::class, $genre->books());
        $this->assertCount(2, $genre->books);
    }

    public function test_genre_name_is_unique_in_the_database(): void
    {
        Genre::factory()->create(['name' => 'ミステリー']);

        $this->expectException(QueryException::class);

        Genre::factory()->create(['name' => 'ミステリー']);
    }
}