<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
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
        $supplierId = $this->route('supplier')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:supplier,name,' . $supplierId,
            ],
            'primary_contact' => ['required', 'email', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'material_certifications' => ['required', 'string', 'max:255'],
            'last_audit_date' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama supplier harus diisi',
            'primary_contact.required' => 'Primary contact harus diisi',
            'location.required' => 'Location harus diisi',
            'material_certifications.required' => 'Material certifications harus diisi',
            'last_audit_date.required' => 'Last audit date harus diisi',
            'primary_contact.email' => 'Format email tidak valid',
        ];
    }
}
