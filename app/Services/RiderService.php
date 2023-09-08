<?php

namespace App\Services;

use App\Interfaces\RiderDetailsInterface;
use App\Interfaces\RiderRepositoryInterface;
use App\Interfaces\RoleInterface;
use App\Interfaces\VehicleTypeInterface;
use App\Models\VehicleType;
use App\Repositories\RiderVehicleRepository;
use App\Repositories\VehicleTypeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RiderService extends BaseService
{
    private RiderRepositoryInterface $rider_repository;
    private RoleInterface $role_repository;
    private $selected_columns = [
        'id',
        'is_active',
        'uuid',
        'name',
        'cnic',
        'phone_number',
        'email',
        'ride_available_status',
        'status',
        'created_at',
        'queue_name',
        'address',
        'notes'
    ];
    private RiderVehicleRepository $rider_vehicle_repository;
    private VehicleTypeInterface $vehicle_type_repository;

    /**
     * @param RiderRepositoryInterface $rider_repository
     * @param RoleInterface $role_repository
     * @param RiderDetailsInterface $rider_details_repository
     * @param RiderVehicleRepository $rider_vehicle_repository
     */
    public function __construct(
        RiderRepositoryInterface $rider_repository,
        RoleInterface            $role_repository,
        RiderVehicleRepository   $rider_vehicle_repository,
        VehicleTypeInterface     $vehicle_type_repository
    )
    {
        $this->rider_repository = $rider_repository;
        $this->role_repository = $role_repository;
        $this->rider_vehicle_repository = $rider_vehicle_repository;
        $this->vehicle_type_repository = $vehicle_type_repository;
    }

    public function register(array $rider_details)
    {
        try {
            $role = $this->role_repository->getData(
                ['name' => 'rider']
            )->firstOrFail();
            $rider_registered = $this->rider_repository->register($rider_details);
            $rider_registered->assignRole($role);
            $otp = generateRandomString(4);
            $phone_number = $rider_details['phone_number'];
            $content = 'Your OTP code is ' . $otp;

            $rider_details_update = [
                'otp' => $otp,
            ];
            $rider_update = $this->rider_repository
                ->update($rider_registered->id,
                    $rider_details_update);
            if ($rider_update) {
                send_sms($phone_number, $content);
            } else {
                $this->serverErrorResponse('Something went wrong while updating rider data.');
            }
            $rider = $this->rider_repository->find($rider_registered->id);
            return [
                'uuid' => $rider->uuid,
                'email' => $rider->email,
            ];
        } catch (\Exception $e) {
            $this->serverErrorResponse($e->getMessage());
        }

    }

    public function setOtpNull($rider)
    {
        return $this->rider_repository->update(
            $rider->id,
            [
                'otp' => null,
                'verfication_attempts' => 0
            ]
        );
    }

    public function verifyOtpAndGenerateToken(array $request_data)
    {
        $selected_columns = $this->selected_columns;
        $query_search_params = [];
        $rider = $this->rider_repository->getData([
            ["uuid", "=", $request_data['uuid']],
            ["otp", "=", $request_data['otp']]
        ],
            $query_search_params,
            $selected_columns
        )->first();
        if (!$rider) {
            $rider = $this->rider_repository->getData(
                [
                    ["uuid", "=", $request_data['uuid']]
                ]
            )->firstOrFail();
            $key = 'verification_failed:' . $rider->id;
            $executed = $this->setRequestLimit($key, 10);
            if (!$executed) {
                $this->setOtpNull($rider);
                $this->errorResponse('To Many attempts! You may try in 60 seconds', 429);
            }
            $rider_attempts = $rider->verfication_attempts + 1;
            if ($rider->otp !== null) {
                $this->rider_repository->update(
                    $rider->id, ['verfication_attempts' => $rider_attempts]
                );
            }
            $this->errorResponse('Otp code did not matched please try again!');
        }
        $this->rider_repository->updateRider($rider, [
            'queue_name' => 'rabbitmq_' . $rider->uuid
        ]);
        $this->setOtpNull($rider);
        $token = $rider->createToken('authToken')->plainTextToken;
        return [
            'token' => $token,
            'rider' => $rider
        ];
    }

    /**
     * @throws \Exception
     */
    public function regenerateOtp(array $request_data)
    {
        $rider = $this->rider_repository->getData([
            ['uuid', '=', $request_data['uuid']]
        ])->first();
        if (!$rider) {
            $this->errorResponse('Record not found against this number.');
        }
        $key = 'resend_otp:' . $rider->id;
        $request_executed = $this->setRequestLimit($key, 5);
        if (!$request_executed) {
            $this->setOtpNull($rider);
            $this->errorResponse('To Many attempts! You may try in 60 seconds', 429);
        }
        $otp = generateRandomString(4);
        $this->rider_repository->update($rider->id,
            ['otp' => $otp, 'verfication_attempts' => 0]
        );
        $content = 'Your OTP code is ' . $otp;
        send_sms($rider->phone_number, $content);
        return [
            'uuid' => $rider->uuid,
            'email' => $rider->email
        ];
    }

    public function getProfileData(array $request_data)
    {
        $selected_columns = $this->selected_columns;
        try {
            return $this->rider_repository->getData(
                [['uuid', '=', $request_data['uuid']]], [],
                $selected_columns, [
                    'riderLicence',
                    'riderSelfiePicture',
                    'riderCnic',
                    'riderContract',
                    'riderVehicle',
                    'riderVehicle.vehicleType'
                ]
            )->first();
        } catch (\Exception $exception) {
            $this->serverErrorResponse($exception->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function updateProfileData(array $request_data, $rider)
    {
        $rider_vehicle = $rider->riderVehicle;
        $vehicle_image = $rider_vehicle->image ?? null;
        $vehicle_color = $rider_vehicle->color ?? null;
        $rider_details = [];
        $rider_details['notes'] = $request_data['notes'] ?? $rider->notes;
        if (isset($request_data['licence'])) {
            $licence = asset(fileUploadViaS3($request_data['licence'],'rider/'.$rider->uuid.'/licence','licence'));
            $this->rider_repository->updateRiderLicence($rider->id, [
                'rider_id' => $rider->id,
                'value' => $licence
            ]);
        }
        if (isset($request_data['selfie_picture'])) {
            $selfie = fileUploadViaS3($request_data['selfie_picture'],'rider/'.$rider->uuid.'/selfie/','selfie');
            $this->rider_repository->updateSelfiePictureStatus($rider->id, [
                'rider_id' => $rider->id,
                'value' => asset($selfie)
            ]);
        }
        if (isset($request_data['cnic_front_pic']) or isset($request_data['cnic_back_pic'])) {
            $rider_cnic = $rider->riderCnic;
            $front_pic = $rider_cnic->front_pic ?? null;
            $back_pic = $rider_cnic->back_pic ?? null;
            if (isset($request_data['cnic_front_pic'])){
                $front_pic = asset(fileUploadViaS3($request_data['cnic_front_pic'],'rider/cnic/'.$rider->uuid.'/front_pic/','front_pic'));
            }
            if (isset($request_data['cnic_back_pic'])) {
                $back_pic = asset(fileUploadViaS3($request_data['cnic_back_pic'],'rider/cnic/'.$rider->uuid.'/back_pic/','back_pic'));
            }
            $this->rider_repository->updateCnicStatus($rider->id, [
                'rider_id' => $rider->id,
                'front_pic' => $front_pic,
                'back_pic' => $back_pic
            ]);
        }
        if (isset($request_data['contract'])) {
            $contract_value = asset(fileUploadViaS3($request_data['contract'],'rider/'.$rider->uuid.'/contract/','contract'));
            $this->rider_repository->updateContractStatus($rider->id, [
                'rider_id' => $rider->id,
                'value' => $contract_value
            ]);
        }
        $this->rider_repository->updateRider($rider, $rider_details);
        if (isset($request_data['vehicle']) && count($request_data['vehicle']) > 0) {
            $vehicle_data = $request_data['vehicle'];
            if (isset($request_data['vehicle']['image'])){
                $vehicle_image = asset(fileUploadViaS3($request_data['vehicle']['image'],'rider/'.$rider->uuid.'/vehicle_image','vehicle_image'));
            }
            $vehicle_type = $this->vehicle_type_repository->find($vehicle_data['vehicle_type_id']);
            if ($vehicle_type) {
                $this->rider_vehicle_repository->updateOrCreate(
                    ['rider_id' => $rider->id], [
                        'rider_id' => $rider->id,
                        'vehicle_type_id' => $vehicle_data['vehicle_type_id'],
                        'registration_number' => $vehicle_data['registration_number'],
                        'image' => $vehicle_image,
                        'color' => $vehicle_data['color'] ?? $vehicle_color,
                    ]
                );
            }
        }
        return $rider;
    }

    /**
     * @param $request_data
     * @param $rider
     * @return bool
     */
    public function updateDeliveryStatus($request_data, $rider)
    {
        $attributes = [
            'is_active' => $request_data['is_active']
        ];
        if (isset($request_data['lat']) and isset($request_data['long'])) {
            $attributes['lat'] = $request_data['lat'];
            $attributes['long'] = $request_data['long'];
        }
        if ($rider->status === "approved") {
            return $this->rider_repository->updateRider($rider, $attributes);
        }
        $this->errorResponse('You cannot do this action because your verification status still in pending.', 403);
    }

    /**
     * @param $request_data
     * @return array
     */
    public function getAllRiders($request_data)
    {
        $data = [];
        $selected_columns = [];
        $search_params = [];
        $search_query = $request_data['search_query'] ?? "";
        $search_query_params = [];
        $limit = $request_data['limit'] ?? 50;
        if (!empty($search_query)) {
            $search_query_params = [
                ['key' => 'name', 'value' => $request_data['search_query']],
                ['key' => 'phone_number', 'value' => $request_data['search_query']]
            ];
        }
        $order_by_params = [
            'id' => 'DESC'
        ];
        try {
            $data = $this->rider_repository->getData(
                $search_params,
                $search_query_params,
                $selected_columns, [], $order_by_params, $limit
            );
        } catch (\Exception $exception) {
            $this->serverErrorResponse($exception->getMessage());
        }
        return [
            'items' => $data->getCollection(),
            'pagination' => paginationData($data)
        ];
    }

    /**
     * @param $request_data
     * @return mixed|void
     */
    public function updateRiderStatus($request_data)
    {
        $rider = $this->rider_repository->getData(
            [
                ['uuid', '=', $request_data['uuid']]
            ]
        )->first();
        if (!$rider) {
            $this->errorResponse('Rider not found.');
        }
        $licence_approved_status = $this->rider_repository->getLicenceStatus($rider->id, 'approved');
        $selfie_picture_status = $this->rider_repository->getSelfiePictureStatus($rider->id, 'approved');
        $cnic_status = $this->rider_repository->getCnicStatus($rider->id, 'approved');
        $contract_status = $this->rider_repository->getContractStatus($rider->id, 'approved');
        $vehicle_approved_status = $this->rider_vehicle_repository->getData(
            [
                ['rider_id', '=', $rider->id],
                ['status', '=', 'approved']
            ]
        )->first();
        $status = $request_data['status'];
        $is_documents_approved = (!$licence_approved_status or !$vehicle_approved_status or !$selfie_picture_status or !$cnic_status or !$contract_status);
        if (($status === 'approved' or $status === 'in_progress') and $is_documents_approved) {
            $this->errorResponse('Status cannot be changed because all required fields not approved.');
        }
        $this->rider_repository->updateRider($rider, [
            'status' => $status
        ]);
        return $rider;
    }

    public function getItem(array $request_data)
    {
        $rider = $this->rider_repository->getData(
            [
                ['uuid', '=', $request_data['uuid']]
            ],
            [], [], [
                'riderSelfiePicture',
                'riderCnic',
                'riderContract',
                'riderLicence',
                'riderVehicle.vehicleType',
            ]
        )->first();
        if (!$rider) {
            $this->errorResponse('Rider not found!');
        }
        return $rider;
    }

    public function sendOtpViaLogin($request_data)
    {
        $rider = $this->rider_repository->getData([
            ['phone_number', '=', $request_data['phone_number']]
        ])->firstOrFail();
        $key = 'login_otp:' . $rider->id;
        $request_executed = $this->setRequestLimit($key, 10);
        if (!$request_executed) {
            $this->setOtpNull($rider);
            $this->errorResponse('To Many attempts! You may try in 60 seconds', 429);
        }
        $otp = generateRandomString(4);
        $this->rider_repository->update($rider->id,
            ['otp' => $otp, 'verfication_attempts' => 0]
        );
        $content = 'Your OTP code is ' . $otp;
        send_sms($rider->phone_number, $content);
        return [
            'uuid' => $rider->uuid,
            'email' => $rider->email
        ];
    }

    public function getCitiesList()
    {
        return $this->rider_repository->getCities();
    }

    public function editFileStatus($request_data)
    {
        $file_status = null;
        if ($request_data['attribute'] === "licence") {
            $file_status = $this->rider_repository->getLicenceStatus(
                null,null,$request_data['attribute_id']
            );
        }elseif ($request_data['attribute'] === "cnic") {
            $file_status = $this->rider_repository->getCnicStatus(
                null,null,$request_data['attribute_id']
            );
        }elseif ($request_data['attribute'] === "contract") {
            $file_status = $this->rider_repository->getContractStatus(
                null,null,$request_data['attribute_id']
            );
        }else{
            $file_status = $this->rider_repository->getSelfiePictureStatus(
                null,null,$request_data['attribute_id']
            );
        }
        if ($file_status) {
            $file_status->update([
                'status' => $request_data['status'],
                'description' => $request_data['description'] ?? null
            ]);
        }
        return $file_status;
    }
}
