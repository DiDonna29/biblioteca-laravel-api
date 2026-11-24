<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
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
        $currentYear = (int) date('Y');

        return [
            'title' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'author' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'published_year' => ['sometimes', 'required', 'integer', 'min:1000', 'max:' . ($currentYear + 1)],
            'category_id' => ['sometimes', 'required', 'integer', 'exists:categories,id'],
        ];
    }

    /**
     * Mensajes personalizados de validación en español.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'El título del libro no puede estar vacío.',
            'title.min' => 'El título debe tener al menos 3 caracteres.',
            'title.max' => 'El título no puede exceder los 255 caracteres.',
            'author.required' => 'El autor del libro no puede estar vacío.',
            'author.max' => 'El nombre del autor no puede exceder los 255 caracteres.',
            'published_year.required' => 'El año de publicación es obligatorio.',
            'published_year.integer' => 'El año de publicación debe ser un número entero válido.',
            'published_year.min' => 'El año de publicación debe ser mayor o igual al año 1000.',
            'published_year.max' => 'El año de publicación no puede ser en el futuro lejano.',
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists' => 'La categoría seleccionada no existe en el sistema.',
        ];
    }
}
