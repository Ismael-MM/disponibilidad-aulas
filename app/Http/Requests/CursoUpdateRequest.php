<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CursoUpdateRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string'],
            'turno' => ['required', 'in:M,T'],
            'horas' => ['required', 'integer', 'gt:0'],
            'horas_diarias' => ['required', 'integer', 'gt:0'],
        ];
    }
}
