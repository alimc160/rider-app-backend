<?php

namespace App\Http\Requests\Rider;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'notes' => 'max:250',
            'licence' => 'sometimes|required|mimes:jpeg,png,webp,bmp,pdf,jpg',
            'selfie_picture' => 'sometimes|required|mimes:jpeg,png,webp,bmp,pdf,jpg',
            'cnic_front_pic' => 'sometimes|required|mimes:jpeg,png,webp,bmp,pdf,jpg',
            'cnic_back_pic' => 'sometimes|required|mimes:jpeg,png,webp,bmp,pdf,jpg',
            'contract' => 'sometimes|required|mimes:jpeg,png,webp,bmp,pdf,jpg',
            'vehicle.registration_number' => 'sometimes|required',
            'vehicle.vehicle_type_id' => 'required_with:vehicle|integer',
            'vehicle.image' => 'required_with:vehicle|mimes:jpeg,png,webp,bmp,pdf,jpg',
        ];
    }

    public function messages()
    {
        return [
            'vehicle.vehicle_type_id.required_with' => "The vehicle type field is required.",
            'vehicle.image.required_with' => "The vehicle image field is required."
        ];
    }

    public function attributes()
    {
        return[
            "vehicle.registration_number" => 'registration number',
            "vehicle.vehicle_type_id" => 'vehicle type',
            "vehicle.image" => 'vehicle image',
            'cnic_front_pic' => 'CNIC front picture',
            'cnic_back_pic' => 'CNIC back picture',
        ];
    }
}
