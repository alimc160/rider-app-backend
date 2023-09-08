<?php

namespace App\Services;

use App\Http\Traits\ApiResponserTrait;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BaseService
{
    use ApiResponserTrait;

    public function setRequestLimit($key, $limit): bool
    {
        return RateLimiter::attempt(
            $key,
            $limit,
            function () {
                return 'Attempt passed';
            }
        );
    }

    public function getRidersList()
    {
        return [
            [
                'lat' => 31.50860340,
                'long' => 74.3320321,
                'uuid' => 'e97ca0ba-58e7-11ed-9c08-181dea028d57',
            ],
            [
                'lat' => 31.50860330,
                'long' => 74.3320310,
                'uuid' => '9c069cb4-626e-11ed-9a51-181dea028d57',
            ],
            [
                'lat' => 31.5159977,
                'long' => 74.3428656,
                'uuid' => 'c2991fcb-626e-11ed-9a51-181dea028d57',
            ],
            [
                'lat' => 31.5159977,
                'long' => 74.3428656,
                'uuid' => 'c9eb4d63-626e-11ed-9a51-181dea028d57',
            ]
        ];
    }

}

?>
