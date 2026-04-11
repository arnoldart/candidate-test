<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CltLayupRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:Active,Draft,Archived',
            'created_by' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama layup harus diisi',
            'status.required' => 'Status harus dipilih',
            'created_by.required' => 'Pembuat (Created By) harus diisi',
        ];
    }
}
