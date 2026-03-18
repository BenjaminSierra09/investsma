<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreListingInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'mensaje' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'Necesitamos tu nombre.',
            'email.required' => 'Necesitamos tu correo electrónico.',
            'email.email' => 'Ingresa un correo válido.',
            'mensaje.required' => 'Cuéntanos qué te interesa de esta propiedad.',
        ];
    }
}
