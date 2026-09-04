<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StoreBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreBookRequestTest extends TestCase
{
    use RefreshDatabase;

    private function rules(): array
    {
        return (new StoreBookRequest())->rules();
    }

    private function baseData(array $overrides = []): array
    {
        $genre = Genre::factory()->create();

        return array_merge([
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890123',
            'published' => '2024-01-01',
            'detail' => 'テスト説明文',
            'picture' => 'https://example.com/cover.jpg',
            'genres' => [$genre->id],
        ], $overrides);
    }

    public function test_valid_input_passes_validation(): void
    {
        $validator = Validator::make($this->baseData(), $this->rules());

        $this->assertTrue($validator->passes());
    }

    public function test_title_is_required(): void
    {
        $validator = Validator::make($this->baseData(['title' => '']), $this->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
    }

    public function test_author_is_required(): void
    {
        $validator = Validator::make($this->baseData(['author' => '']), $this->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('author', $validator->errors()->toArray());
    }

    public function test_isbn_is_required(): void
    {
        $validator = Validator::make($this->baseData(['isbn' => '']), $this->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('isbn', $validator->errors()->toArray());
    }

    public function test_isbn_must_be_unique(): void
    {
        Book::factory()->create(['isbn' => '9999999999999']);

        $validator = Validator::make($this->baseData(['isbn' => '9999999999999']), $this->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('isbn', $validator->errors()->toArray());
    }

    public function test_published_date_must_be_a_valid_date(): void
    {
        $validator = Validator::make($this->baseData(['published' => 'not a date']), $this->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('published', $validator->errors()->toArray());
    }

    public function test_at_least_one_genre_is_required(): void
    {
        $validator = Validator::make($this->baseData(['genres' => []]), $this->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('genres', $validator->errors()->toArray());
    }

    public function test_genre_id_must_exist(): void
    {
        $validator = Validator::make($this->baseData(['genres' => [9999]]), $this->rules());

        $this->assertTrue($validator->fails());
    }

    public function test_picture_must_be_a_valid_url(): void
    {
        $validator = Validator::make($this->baseData(['picture' => 'not a url']), $this->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('picture', $validator->errors()->toArray());
    }

    public function test_picture_is_optional(): void
    {
        $data = $this->baseData();
        unset($data['picture']);

        $validator = Validator::make($data, $this->rules());

        $this->assertTrue($validator->passes());
    }
}
