<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'movie_id' => 'required|exists:movies,id',
            'studio_id' => 'required|exists:studios,id',
            'show_date' => 'required|date|after_or_equal:today',
            'show_time' => 'required|date_format:H:i',
            'price' => 'required|numeric|min:1|max:999999'
        ];
    }
}