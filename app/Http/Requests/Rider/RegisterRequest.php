<?php

namespace App\Http\Requests\Rider;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|max:255',
            'email' => 'required|email|unique:riders',
            'father_name' => 'required',
            'cnic' => 'required|unique:riders|regex:/^([0-9]{5})[\-]([0-9]{7})[\-]([0-9]{1})+/',
            'city_id' => 'required|integer|min:1',
            'phone_number' => 'required|phone:PK|unique:riders',
            'address' => 'required'
        ];
    }

    public function attributes()
    {
        return[
          'city_id' => 'city',
          'phone_number' => 'contact',
          'cnic' => 'CNIC'
        ];
    }
}
