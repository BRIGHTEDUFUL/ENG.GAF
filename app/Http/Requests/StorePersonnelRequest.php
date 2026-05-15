<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePersonnelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Authorization is handled by PersonnelPolicy, not the Form Request.
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
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'max:255', 'unique:personnel,email'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'department' => ['required', 'string', 'max:100'],
            'position'   => ['required', 'string', 'max:100'],
            'hire_date'  => ['required', 'date', 'before_or_equal:today'],
            'status'     => ['required', 'in:active,inactive'],
            'avatar'     => ['nullable', 'string'],
            'notes'      => ['nullable', 'string'],
        ];
    }
}
