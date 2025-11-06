<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovieRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'genre' => 'required|string|max:100',
            'duration' => 'required|integer|min:1',
            'status' => 'required|in:coming_soon,live_now',
            'description' => 'required|string',
            'rating' => 'required|in:G,PG,PG-13,R,NC-17',
            'director' => 'required|string|max:255',
            'production_team' => 'nullable|string',
            'trailer_type' => 'required|in:url,upload',
            'trailer_url' => 'required_if:trailer_type,url|nullable|url',
            'trailer_file' => 'required_if:trailer_type,upload|nullable|file|mimes:mp4,avi,mov|max:51200',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'price' => 'nullable|numeric|min:0'
        ];
    }
}