<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AdminService $admin_service;

    public function __construct(AdminService $admin_service)
    {
        $this->admin_service = $admin_service;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
        $input = $request->only(['email','password']);
        $login_response = $this->admin_service->authenticate($input);
        return $this->successResponse('Logged in successfully!',$login_response);
    }

    /**
     * @return JsonResponse
     */
    public function logout()
    {
        \request()->user()->tokens()->delete();
        return $this->successResponse('Logout successfully.');
    }
}
