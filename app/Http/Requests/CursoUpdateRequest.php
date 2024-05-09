<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
                'titulo' => ['required', 'max:191'],
                'codigo' => ['required', 'max:20'],
                'horas' => ['required', 'numeric', 'min:1', 'max:3000'],
                'horas_diarias' => ['required', 'numeric', 'min:1', 'max:10'],
                'calidad' => ['required', 'numeric', 'min:1', 'max:5'],
        ];
    }
}
