<?php

namespace App\Services;

use App\Repositories\AdminRepository;
use Illuminate\Support\Facades\Hash;

class AdminService extends BaseService
{
    /**
     * @var AdminRepository
     */
    private $admin_repository;

    public function __construct(AdminRepository $admin_repository)
    {
        $this->admin_repository = $admin_repository;
    }

    public function authenticate($request_data)
    {
        $admin = $this->admin_repository->getData(
            [
                ['email', '=', $request_data['email']]
            ],
            [],
            ['id', 'name','password', 'email', 'created_at']
        )->first();
        if (!$admin) {
            $this->errorResponse('Invalid credentials.');
        }
        if (!Hash::check($request_data['password'], $admin->password)) {
            $this->errorResponse('Invalid credentials.');
        }
        $token = $admin->createToken('authToken')->plainTextToken;
        return [
            'token' => $token,
            'admin' => $admin
        ];

    }
}
