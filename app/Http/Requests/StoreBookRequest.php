<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => ['nullable', 'digits:13'],
            'published_at' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'genre_ids' => ['required', 'array', 'min:1', 'integer', 'exists:genres,id'],
        ];
    }
    public function messages(): array
    {
        return [
            'isbn.digits'      => 'ISBNは13桁の数字で入力してください。',
            'genre_ids.required' => 'ジャンルを1つ以上選択してください。',
        ];
    }
}
