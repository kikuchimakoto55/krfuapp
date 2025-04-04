<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRequest extends FormRequest
{
    public function authorize()
    {
        return true; // 認証済みのユーザーのみ許可する場合は制御可能
    }

    public function rules()
    {
        return [
        'grade_category' => 'required|integer',
        'username_sei' => 'required|string|max:15',
        'username_mei' => 'required|string|max:15',
        'email' => 'nullable|email',
        'coach_flg' => 'required|integer|in:0,1,2',
        'username_kana_s' => 'required|string|regex:/^[ァ-ヶー]+$/u|max:30',
        'username_kana_m' => 'required|string|regex:/^[ァ-ヶー]+$/u|max:30',
        'sex' => 'required|in:1,2',
        'username_en_s' => 'required|string|max:50',
        'username_en_m' => 'required|string|max:50',
        'birthday' => 'required|date',
        'height' => 'nullable|numeric|min:50|max:250',
        'weight' => 'nullable|numeric|min:10|max:250',
        'blood_type' => 'nullable|in:1,2,3,4,5',
        'zip' => 'required|digits:7',
        'address1' => 'required|string|max:100',
        'address2' => 'required|string|max:100',
        'address3' => 'nullable|string|max:100',
        'enrolled_school' => 'nullable|string|max:100',
        'guardian_name' => 'required|string|max:100',
        'guardian_email' => 'required|email|max:255',
        'guardian_tel' => 'required|digits_between:10,11',
        'relationship' => 'required|in:1,2,3,4,5,6',
        'emergency_name1' => 'required|string|max:100',
        'emergency_email1' => 'required|email|max:255',
        'emergency_tel1' => 'required|digits_between:10,11',
        'tel' => 'nullable|digits_between:10,11',
        'remarks' => 'nullable|string|max:500',
        'classification' => 'required|in:1,2,3,4,5,6,7,8,9',
        'membershipfee_conf' => 'nullable|string|max:100',
        'association_id' => 'nullable|string|max:100',
        'status' => 'required|in:1,2,3,4,5,6',
        'graduation_year' => 'nullable|integer|min:1900|max:2100',
        'authoritykinds_id' => 'required|in:1,2,3,4',
        'del_flg' => 'required|in:0,1',
        'password' => 'nullable|string|min:8|confirmed',
        ];
    }
    public function messages(): array
{
    return [
        // ▼ カナの形式
        'username_kana_s.regex' => '氏名カナ(姓)は全角カタカナで入力してください。',
        'username_kana_m.regex' => '氏名カナ(名)は全角カタカナで入力してください。',

        // ▼ 電話番号・郵便番号形式
        'zip.digits' => '郵便番号は7桁の半角数字で入力してください。',
        'guardian_tel.digits_between' => '保護者電話番号は10～11桁の半角数字で入力してください。',
        'emergency_tel1.digits_between' => '緊急連絡先電話番号は10～11桁の半角数字で入力してください。',
        'tel.digits_between' => '本人電話番号は10～11桁の半角数字で入力してください。',

        // ▼ パスワード
        'password.confirmed' => 'パスワード確認が一致していません。',
        'password.min' => 'パスワードは8文字以上で入力してください。',

        // ▼ メールアドレス形式
        'guardian_email.email' => '保護者メールアドレスの形式が正しくありません。',
        'emergency_email1.email' => '緊急連絡先メールアドレスの形式が正しくありません。',
        'email.email' => '本人メールアドレスの形式が正しくありません。',

        // ▼ 必須項目（required）
        'grade_category.required' => '学年カテゴリは必須です。',
        'username_sei.required' => '氏名（姓）は必須です。',
        'username_mei.required' => '氏名（名）は必須です。',
        'username_kana_s.required' => '氏名カナ（姓）は必須です。',
        'username_kana_m.required' => '氏名カナ（名）は必須です。',
        'sex.required' => '性別は必須です。',
        'username_en_s.required' => '氏名（姓）英は必須です。',
        'username_en_m.required' => '氏名（名）英は必須です。',
        'birthday.required' => '生年月日は必須です。',
        'zip.required' => '郵便番号は必須です。',
        'address1.required' => '都道府県は必須です。',
        'address2.required' => '市区町村は必須です。',
        'guardian_name.required' => '保護者氏名は必須です。',
        'guardian_email.required' => '保護者メールアドレスは必須です。',
        'guardian_tel.required' => '保護者電話番号は必須です。',
        'relationship.required' => '続柄は必須です。',
        'emergency_name1.required' => '緊急連絡先氏名は必須です。',
        'emergency_email1.required' => '緊急連絡先メールアドレスは必須です。',
        'emergency_tel1.required' => '緊急連絡先電話番号は必須です。',
        'classification.required' => '所属区分は必須です。',
        'status.required' => '在籍状況は必須です。',
        'authoritykinds_id.required' => '権限種別は必須です。',
        'coach_flg.required' => '指導員フラグは必須です。',
        'password.required' => 'パスワードは必須です。',
        'password_confirmation.required' => 'パスワード確認は必須です。',
    ];
}
}
