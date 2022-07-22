<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ProductRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        if ($this->method() == 'POST') {
            return [
                'name'=> 'required|unique:products|max:255',
                'price' => 'required|numeric|gt:0',
                'description' => 'required',
                'category' => 'required|max:255',
            ];
        }

        return [
            'name'=> 'filled|unique:products|max:255',
            'price' => 'filled|numeric|gt:0',
            'description' => 'filled',
            'category' => 'filled|max:255',
        ];
    }

    public function messages()
    {
        if ($this->method() == 'POST') {
            return [
                'name.unique' => 'O nome desse produto já está sendo usado. Por favor, informe outro nome.',
                '*.required' => 'Campo obrigatório.',
                '*.max' => 'O campo não pode ultrapassar de 255 caracteres.',
                'price.*' => 'Campo obrigatório. O preço deve ser um número maior que zero.'
            ];
        }

        return [
            'name.unique' => 'O nome desse produto já está sendo usado. Por favor, informe outro nome.',
            '*.filled' => 'Esse campo não pode ser vazio.',
            '*.max' => 'O campo não pode ultrapassar de 255 caracteres.',
            'price.*' => 'O preço deve ser um número maior que zero.'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
