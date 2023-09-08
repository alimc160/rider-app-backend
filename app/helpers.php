<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

/**
 * HTTP Client for sending sms.
 *
 */
if (!function_exists('send_sms')) {
    /**
     * @throws Exception
     */
    function send_sms($phone_number, $content)
    {
        if (empty($phone_number)) {
            throw new Exception("Phone number for sms is required");
        } elseif (empty($content)) {
            throw new Exception("Content for sms is required");
        }
        return Http::asForm()->post('http://35.159.45.150/bookme-vpn/public/index.php/notify/sms', [
            "phone_number" => $phone_number,
            "country_code" => "PK",
            "content" => $content,
        ]);
    }
}

/**
 * method for generating random string.
 *
 */
if (!function_exists('generateRandomString')) {
    /**
     * @param int $length
     * @return string
     */
    function generateRandomString(int $length = 5): string
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
if (!function_exists('parseUrl')) {
    /**
     * @param $url
     * @return mixed|string
     */
    function parseUrl($url)
    {
        $parse_url = parse_url($url);
        return $parse_url['path'];
    }
}
if (!function_exists('fileUpload')) {
    /**
     * @param $base64_encoded_file
     * @param string $path
     * @param string $previous_store_file_path
     * @param array $allowed_extensions
     * @param string $file_title
     * @return string
     */
    function fileUpload($base64_encoded_file, string $path = '/app', string $previous_store_file_path = null, array $allowed_extensions = [], $file_title = 'test'): string
    {
        try {
            $is_file_exist = File::exists(public_path() . $previous_store_file_path);
            if ($is_file_exist) {
                File::delete(public_path() . $previous_store_file_path);
            }
            $extension = explode('/', explode(':', substr($base64_encoded_file, 0, strpos($base64_encoded_file, ';')))[1])[1];
            if (!in_array($extension, $allowed_extensions)) {
                throw new Exception('File is not valid extension.');
            }
            $replace = substr($base64_encoded_file, 0, strpos($base64_encoded_file, ',') + 1);
            $file = str_replace($replace, '', $base64_encoded_file);
            $file = str_replace(' ', '+', $file);
            $file_name = $file_title . '.' . $extension;
            Storage::disk('public')->put($path . '/' . $file_name, base64_decode($file));
            $saved_file_path = Storage::url('public/' . $path . '/' . $file_name);
            return preg_replace('/([^:])(\/{2,})/', '$1/', $saved_file_path);
        } catch (\Exception $exception) {
            throw new Exception($exception->getMessage());
        }

    }
}

if (!function_exists('fileUploadViaS3')) {
    /**
     * @throws Exception
     */
    function fileUploadViaS3($file, $path = 'app/', $title = ''): string
    {
        try {
            $extension = $file->getClientOriginalExtension();
            $file_name = uniqid() . '_' . $title . '.' . $extension;
            $file_saved_path = Storage::disk('public')->put($path . '/' . $file_name, $file);
            return 'storage/' . $file_saved_path;
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}

if (!function_exists('paginationData')) {
    function paginationData($data): array
    {
        return [
            "per_page" => $data->perPage(),
            "current_page" => $data->currentPage(),
            "total" => $data->total(),
            "last_page" => $data->lastPage(),
            "from" => $data->toArray()['from'],
            "to" => $data->toArray()['to']
        ];
    }
}

function getaddress($lat, $lng, $key)
{
    $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim($lat) . ',' . trim($lng) . '&sensor=false&key=' . $key;
    $json = @file_get_contents($url);
    $data = json_decode($json);
    $status = $data->status;
    if ($status == "OK") {
        return $data->results[0]->formatted_address;
    } else {
        return false;
    }
}

function getDistanceTime($origin, $destination, $key)
{
    $url = 'https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $origin . '&destinations=' . $destination . '&key=' . $key;
//   dd($url);
    $json = @file_get_contents($url);
    $data = json_decode($json);
    dd($data);
}

if (!function_exists('haversineGreatCircleDistance')) {
    function haversineGreatCircleDistance(
        $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000
    )
    {
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
}

if (!function_exists('getNearestLocations')) {
    function getNearestLocations($locations, $base_location_lat, $base_location_long)
    {
        $distances = [];
        foreach ($locations as $key => $location) {
            $a = $base_location_lat - $location['lat'];
            $b = $base_location_long - $location['long'];
            $distance = sqrt(($a ** 2) + ($b ** 2));
            $distances[$key] = $distance;
        }
        return asort($distances);
    }
}
