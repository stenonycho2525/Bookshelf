<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateBookRequestTest extends TestCase
{
    use RefreshDatabase;
    private function makeRequest(Book $book, array $data): UpdateBookRequest
    {
        $request = UpdateBookRequest::create("/books/{$book->id}", 'PUT', $data);

        $route = new Route('PUT', '/books/{book}', []);
        $route->bind($request);
        $route->setParameter('book', (string) $book->id);
        $request->setRouteResolver(fn() => $route);

        return $request;
    }

    private function baseData(Book $book, array $overrides = []): array
    {
        $genre = Genre::factory()->create();

        return array_merge([
            'title' => $book->title,
            'author' => $book->author,
            'isbn' => $book->isbn,
            'published' => $book->published->format('Y-m-d'),
            'detail' => $book->detail,
            'genres' => [$genre->id],
        ], $overrides);
    }

    public function test_keeping_own_isbn_unchanged_does_not_error(): void
    {
        $book = Book::factory()->create(['isbn' => '1111111111111']);
        $request = $this->makeRequest($book, $this->baseData($book, ['isbn' => '1111111111111']));

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertTrue($validator->passes());
    }

    public function test_changing_to_an_isbn_used_by_another_book_errors(): void
    {
        $book = Book::factory()->create(['isbn' => '1111111111111']);
        $otherBook = Book::factory()->create(['isbn' => '2222222222222']);

        $request = $this->makeRequest($book, $this->baseData($book, ['isbn' => $otherBook->isbn]));

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('isbn', $validator->errors()->toArray());
    }

    public function test_title_is_required(): void
    {
        $book = Book::factory()->create();
        $request = $this->makeRequest($book, $this->baseData($book, ['title' => '']));

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
    }

    public function test_at_least_one_genre_is_required(): void
    {
        $book = Book::factory()->create();
        $request = $this->makeRequest($book, $this->baseData($book, ['genres' => []]));

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('genres', $validator->errors()->toArray());
    }
}
