<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'title' => 'required|string|max:255',
            'isbn' => [
                'required',
                'string',
                Rule::unique('books', 'isbn')->ignore($this->route('book')?->id)
            ],
            'description' => 'nullable|string|max:255',
            'author_id' => 'required|exists:authors,id',
            'genre' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
            'total_copies' => 'required|integer',
            'available_copies' => 'nullable|integer',
            'price' => 'nullable|numeric',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,avif,gif,svg|max:2048',
            'status' => 'nullable|in:active,inactive'
        ];
    }
}
