<?php

namespace App\Repositories;

use App\Interfaces\RiderRepositoryInterface;
use App\Models\City;
use App\Models\Rider;
use App\Models\RiderCnic;
use App\Models\RiderContract;
use App\Models\RiderLicence;
use App\Models\RiderLocationLog;
use App\Models\RiderMedia;
use App\Models\RiderSelfiePicture;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class RiderRepository extends BaseRepository implements RiderRepositoryInterface
{
    private $rider_detail;
    private RiderLocationLog $rider_location_log;
    private City $city;
    private RiderMedia $rider_media;
    private RiderLicence $rider_licence_status;
    private RiderSelfiePicture $rider_selfie_picture_status;
    private RiderCnic $rider_cnic_status;
    private RiderContract $rider_contract_status;
    public function __construct(
        Rider               $model,
        RiderLocationLog    $rider_location_log,
        City                $city,
        RiderMedia          $rider_media,
        RiderLicence        $rider_licence_status,
        RiderSelfiePicture  $rider_selfie_picture_status,
        RiderCnic           $rider_cnic_status,
        RiderContract $rider_contract_status
    )
    {
        parent::__construct($model);
        $this->rider_location_log = $rider_location_log;
        $this->city = $city;
        $this->rider_media = $rider_media;
        $this->rider_licence_status = $rider_licence_status;
        $this->rider_selfie_picture_status = $rider_selfie_picture_status;
        $this->rider_cnic_status = $rider_cnic_status;
        $this->rider_contract_status = $rider_contract_status;
    }

    /**
     * @param object $rider
     * @param array $attributes
     * @return bool
     */
    public function updateRider(object $rider, array $attributes): bool
    {
        return $rider->update($attributes);
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function addRiderLocationLogs(array $attributes): Model
    {
        return $this->rider_location_log->create($attributes);
    }

    /**
     * @return Collection
     */
    public function getCities(): Collection
    {
        return $this->city::orderBy('name', 'ASC')->get();
    }

    /**
     * @param array $exiting_attributes
     * @param array $update_attributes
     * @return Model
     */
    public function updateOrCreateMedia(array $exiting_attributes, array $update_attributes): Model
    {
        return $this->rider_media->updateOrCreate($exiting_attributes, $update_attributes);
    }

    /**
     * @param int $rider_id
     * @param array $attributes
     * @return Model
     */
    public function updateRiderLicence(int $rider_id, array $attributes): Model
    {
        return $this->rider_licence_status->updateOrCreate(
            [
                'rider_id' => $rider_id
            ],
            $attributes
        );
    }

    /**
     * @param int $rider_id
     * @param array $attributes
     * @return Model
     */
    public function updateSelfiePictureStatus(int $rider_id, array $attributes): Model
    {
        return $this->rider_selfie_picture_status->updateOrCreate(
            [
                'rider_id' => $rider_id
            ],
            $attributes
        );
    }

    /**
     * @param int $rider_id
     * @param array $attributes
     * @return Model
     */
    public function updateCnicStatus(int $rider_id, array $attributes): Model
    {
        return $this->rider_cnic_status->updateOrCreate(
            [
                'rider_id' => $rider_id
            ],
            $attributes
        );
    }

    /**
     * @param int $rider_id
     * @param array $attributes
     * @return Model
     */
    public function updateContractStatus(int $rider_id, array $attributes): Model
    {
        return $this->rider_contract_status->updateOrCreate(
            [
                'rider_id' => $rider_id
            ],
            $attributes
        );
    }

    /**
     * @param int|null $rider_id
     * @param string|null $status
     * @param $id
     * @return Model|null
     */
    public function getLicenceStatus(int $rider_id = null, string $status = null, $id = null): ?Model
    {
        $query = $this->rider_licence_status;
        if (isset($rider_id)) {
            $query = $query->whereRiderId($rider_id);
        }
        if (isset($status) && !empty($status)) {
            $query = $query->whereStatus($status);
        }
        if (isset($id) && !empty($id)) {
            $query = $query->whereId($id);
        }
        return $query->first();
    }

    /**
     * @param int|null $rider_id
     * @param string|null $status
     * @param int|null $id
     * @return Model|null
     */
    public function getSelfiePictureStatus(int $rider_id = null, string $status=null,int $id = null): ?Model
    {
        $query = $this->rider_selfie_picture_status;
        if (isset($rider_id)) {
            $query = $query->whereRiderId($rider_id);
        }
        if (isset($status) && !empty($status)) {
            $query = $query->whereStatus($status);
        }
        if (isset($id) && !empty($id)) {
            $query = $query->whereId($id);
        }
        return $query->first();
    }

    /**
     * @param int|null $rider_id
     * @param string|null $status
     * @param int|null $id
     * @return Model|null
     */
    public function getCnicStatus(int $rider_id = null, string $status = null,int $id = null): ?Model
    {
        $query = $this->rider_cnic_status;
        if (isset($rider_id)) {
            $query = $query->whereRiderId($rider_id);
        }
        if (isset($status) && !empty($status)) {
            $query = $query->whereStatus($status);
        }
        if (isset($id) && !empty($id)) {
            $query = $query->whereId($id);
        }
        return $query->first();
    }

    /**
     * @param int|null $rider_id
     * @param string|null $status
     * @param int|null $id
     * @return Model|null
     */
    public function getContractStatus(int $rider_id=null, string $status = null,int $id = null): ?Model
    {
        $query = $this->rider_contract_status;
        if (!empty($rider_id)) {
           $query = $query->whereRiderId($rider_id);
        }
        if (isset($status) && !empty($status)) {
            $query = $query->whereStatus($status);
        }
        if (isset($id) && !empty($id)) {
            $query = $query->whereId($id);
        }
        return $query->first();
    }

    /**
     * @param array $where_params
     * @param array $where_in_params
     * @param array $order_by_params
     * @param array $relations
     * @param array $select_columns
     * @param bool $have_sine_calculation
     * @param int $radius
     * @param $lat
     * @param $long
     * @return Collection
     */
    public function all(
        array $where_params = [],
        array $where_in_params = [],
        array $order_by_params = [],
        array $relations = [],
        array $select_columns = [],
        bool $have_sine_calculation = false,
        int $radius = 1,
        $lat = 0,
        $long = 0): Collection
    {
        $query = $this->model;
        if (count($where_params)) {
            $query = $query->where($where_params);
        }
        if (count($where_in_params)) {
            $query = $query->whereIntegerInRaw($where_in_params['column'], $where_in_params['values']);
        }
        foreach ($order_by_params as $key => $by_param) {
            $query = $query->orderBy($key, $by_param);
        }
        if (count($relations) > 0) {
            $query = $query->with($relations);
        }
        if ($have_sine_calculation) {
            $query = $query->selectRaw(
                '( 6371 * acos( cos( radians(?) ) *
                               cos( radians(riders.lat) ) *
                                cos( radians(riders.long) - radians(?))
                                + sin(radians(?)) *
                               sin( radians(riders.lat)) )
                             ) AS distance',
                [$lat, $long, $lat]
            );
            $query = $query->havingRaw('distance <= ?', [$radius]);
        }
        if (count($select_columns) > 0) {
            $query = $query->addSelect($select_columns);
        }
        return $query->get();
    }
}
