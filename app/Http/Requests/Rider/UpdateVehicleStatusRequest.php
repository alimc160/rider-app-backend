<?php

namespace App\Http\Requests\Rider;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleStatusRequest extends FormRequest
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
            'rider_vehicle_id' => 'required|integer',
            'status' => 'required|in:in_progress,approved,cancelled',
            'description' => 'max:250',
        ];
    }

    public function attributes()
    {
        return[
            "rider_vehicle_id" => 'vehicle',
        ];
    }
}
