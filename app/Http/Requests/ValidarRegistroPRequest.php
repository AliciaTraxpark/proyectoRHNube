<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarRegistroPRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nombres'        => 'required|min:1|max:45',
            'apellidos'      => 'required|min:1|max:45',
            'usuario'        => 'required',
            'password'       => 'required',
            'fecha'          => 'required'
        ];
    }

    public function messages()
    {
        return[
            'nombres.required'   => '*El :attribute es obligatorio.',
            'apellidos.required'   => '*El :attribute es obligatorio.',
            'usuario.required'   => '*El :attribute es obligatorio.',
            'password.required'   => '*El :attribute es obligatorio.',
            'fecha.required'   => '*La :attribute es obligatoria.',
        ];
    }
}
