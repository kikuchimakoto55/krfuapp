<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
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
       'grade_category' => ['required', 'integer', 'in:1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21'],
        'username_sei' => ['required', 'string', 'max:15'],
        'username_mei' => ['required', 'string', 'max:15'],
        'username_kana_s' => ['required', 'regex:/^[ァ-ヶー]+$/u'],
        'username_kana_m' => ['required', 'regex:/^[ァ-ヶー]+$/u'],
        'sex' => ['required', 'integer', 'in:1,2'],
        'username_en_s' => ['required', 'string'],
        'username_en_m' => ['required', 'string'],
        'birthday' => ['required', 'date'],
        'height' => ['nullable', 'numeric'],
        'weight' => ['nullable', 'numeric'],
        'blood_type' => ['nullable', 'integer', 'in:1,2,3,4,5'],
        'zip' => ['required', 'digits:7'],
        'address1' => ['required', 'string'],
        'address2' => ['required', 'string'],
        'address3' => ['nullable', 'string'],
        'enrolled_school' => ['nullable', 'string'],
        'guardian_name' => ['required', 'string'],
        'guardian_email' => ['required', 'email'],
        'guardian_tel' => ['required', 'digits_between:10,11'],
        'relationship' => ['required', 'integer', 'in:1,2,3,4,5'],
        'emergency_name1' => ['required', 'string'],
        'emergency_email1' => ['required', 'email'],
        'emergency_tel1' => ['required', 'digits_between:10,11'],
        'email' => ['nullable', 'email', 'unique:t_members,email'],
        'tel' => ['nullable', 'digits_between:10,11'],
        'remarks' => ['nullable', 'string'],
        'classification' => ['required', 'integer', 'in:1,2,3,4,5,6,7,8'],
        'membershipfee_conf' => ['nullable', 'string'],
        'association_id' => ['nullable', 'string'],
        'status' => ['required', 'integer', 'in:1,2,3,4,5,6'],
        'graduation_year' => ['nullable', 'digits:4'],
        'authoritykinds_id' => ['required', 'integer'],
        'coach_flg' => ['required', 'integer', 'in:0,1'],
        'password' => ['required', 'confirmed'],
    ];
    }
}
