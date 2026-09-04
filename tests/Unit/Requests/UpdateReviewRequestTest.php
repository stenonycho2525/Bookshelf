<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\UpdateReviewRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateReviewRequestTest extends TestCase
{
    private function rules(): array
    {
        return (new UpdateReviewRequest())->rules();
    }

    private function baseData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'テストレビュー',
            'rate' => 3,
            'comment' => '更新後のコメント',
        ], $overrides);
    }

    public function test_valid_input_passes_validation(): void
    {
        $validator = Validator::make($this->baseData(), $this->rules());

        $this->assertTrue($validator->passes());
    }

    public function test_rate_out_of_range_errors(): void
    {
        $validator = Validator::make($this->baseData(['rate' => 10]), $this->rules());

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
