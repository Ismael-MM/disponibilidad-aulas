<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AulaCursoRequest extends FormRequest
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
            'aula_id' => ['required', 'exists:aulas,id'],
            'curso_id' => ['required', 'exists:cursos,id'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date'],
        ];
    }
}
