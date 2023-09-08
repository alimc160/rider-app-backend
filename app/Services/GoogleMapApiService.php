<?php

namespace App\Services;

use App\Http\Traits\ApiResponserTrait;
use Illuminate\Support\Facades\Http;

class GoogleMapApiService extends BaseService
{
    /**
     * @var mixed
     */
    private $map_api_key;

    public function __construct()
    {
        $this->map_api_key = env('GOOGLE_MAP_API_KEY');
    }

    /**
     * @param $origin
     * @param $destination
     * @return array|void
     */
    public function distanceMatrix($origin, $destination)
    {
        try {
            $response = Http::get("https://maps.googleapis.com/maps/api/distancematrix/json?departure_time=now&destinations=$destination&origins=$origin&key=$this->map_api_key")->json();
            return [
                'distance' => $response['rows'][0]['elements'][0]['distance']['text'],
                'duration' => $response['rows'][0]['elements'][0]['duration']['text'],
                'duration_in_traffic' => $response['rows'][0]['elements'][0]['duration_in_traffic']['text'],
            ];
        } catch (\Exception $exception) {
            $this->serverErrorResponse($exception->getMessage());
        }
    }

    /**
     * @param float $lat
     * @param float $lng
     * @return mixed
     */
    public function getLocationFromLatLng(float $lat = 31.505074, float $lng = 74.332032)
    {
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lng . '&key=' . $this->map_api_key)->json();
        if ($response['status'] === "REQUEST_DENIED") {
            $this->serverErrorResponse($response['error_message']);
        }
        return $response['results'][0]['formatted_address'];
    }
}
