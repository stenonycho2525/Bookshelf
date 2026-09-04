<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StoreReviewRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreReviewRequestTest extends TestCase
{
    private function rules(): array
    {
        return (new StoreReviewRequest())->rules();
    }

    private function baseData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'テストレビュー',
            'rate' => 4,
            'comment' => 'とても良かったです。',
        ], $overrides);
    }

    public function test_valid_input_passes_validation(): void
    {
        $validator = Validator::make($this->baseData(), $this->rules());

        $this->assertTrue($validator->passes());
    }

    public function test_rate_is_required(): void
    {
        $validator = Validator::make($this->baseData(['rate' => '']), $this->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('rate', $validator->errors()->toArray());
    }

    public function test_rate_of_zero_errors(): void
    {
        $validator = Validator::make($this->baseData(['rate' => 0]), $this->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('rate', $validator->errors()->toArray());
    }

    public function test_rate_of_six_errors(): void
    {
        $validator = Validator::make($this->baseData(['rate' => 6]), $this->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('rate', $validator->errors()->toArray());
    }

    public function test_rate_must_be_an_integer(): void
    {
        $validator = Validator::make($this->baseData(['rate' => 'abc']), $this->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('rate', $validator->errors()->toArray());
    }

    public function test_comment_is_required(): void
    {
        $validator = Validator::make($this->baseData(['comment' => '']), $this->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('comment', $validator->errors()->toArray());
    }
}
