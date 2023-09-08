<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class AddRequest extends FormRequest
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
            'order_id' => 'required',
            'cargo_booking_id' => 'required',
            'pickup_lat' => 'required',
            'pickup_long' => 'required',
            'drop_off_lat' => 'required',
            'drop_off_long' => 'required',
            'booking_amount' => 'required',
            'agent_name' => 'required',
            'agent_contact' => 'required|phone:PK',
            'customer_name' => 'required',
            'customer_contact' => 'required|phone:PK',
            'customer_cnic' => 'required|max:13|regex:/^[0-9]+$/',
            'no_of_packages' => 'required|integer'
        ];
    }
}
