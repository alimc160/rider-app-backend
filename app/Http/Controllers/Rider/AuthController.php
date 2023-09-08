<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rider\RegisterRequest;
use App\Http\Requests\Rider\UpdateProfileRequest;
use Illuminate\Http\Request;
use App\Services\RiderService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    private RiderService $rider_service;

    public function __construct(RiderService $rider_service)
    {
        $this->rider_service = $rider_service;
    }

    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $rider_details = $request->only([
            'name',
            'user_name',
            'father_name',
            'cnic',
            'city_id',
            'phone_number',
            'email',
            'password',
            'address'
        ]);
        $rider_registered = $this->rider_service->register($rider_details);
        return $this->createdResponse('Rider reigistered successfully', $rider_registered);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function generateToken(Request $request)
    {
        $request->validate([
            'uuid' => 'required',
            'otp' => 'required'
        ]);
        $input = $request->only(['uuid', 'otp']);
        $data = $this->rider_service->verifyOtpAndGenerateToken($input);
        return $this->successResponse("Rider verification completed successfully.", $data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'uuid' => 'required'
        ]);
        $input = $request->only(['uuid']);
        $data = $this->rider_service->regenerateOtp($input);
        return $this->successResponse("OTP has been sent.", $data);
    }

    /**
     * @return JsonResponse
     */
    public function getProfile()
    {
        $request = \request()->user()->only('uuid');
        $data = $this->rider_service->getProfileData($request);
        return $this->successResponse('', $data);
    }

    /**
     * @param UpdateProfileRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $input = $request->all();
        $rider = $request->user();
        $this->rider_service->updateProfileData($input, $rider);
        return $this->successResponse('Profile updated successfully!');
    }

    public function logout()
    {
        \request()->user()->tokens()->delete();
        return $this->successResponse('Logout successfully.');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|phone:PK'
        ]);
        $input = $request->only(['phone_number']);
        $response = $this->rider_service->sendOtpViaLogin($input);
        return $this->successResponse("OTP has been sent.", $response);
    }
}
