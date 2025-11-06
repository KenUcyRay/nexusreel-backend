<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studioId = $this->route('studio');
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('studios')->ignore($studioId)
            ],
            'type' => 'required|in:Regular,Premium,IMAX,4DX',
            'status' => 'required|in:active,inactive',
            'rows' => 'required|integer|min:1|max:20',
            'columns' => 'required|integer|min:1|max:20'
        ];
    }
}