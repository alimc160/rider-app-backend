<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class PackageDeliveredRequest extends FormRequest
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
            'booking_uuid' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'receiver_pic' => 'required|mimes:jpeg,png,webp,bmp,pdf,jpg',
            'receiver_name' => 'required|max:255',
            'receiver_cnic' => 'required|max:255',
            'receiver_contact' => 'required|phone:PK',
        ];
    }
}
