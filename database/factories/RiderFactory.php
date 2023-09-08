<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RiderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'user_name' => $this->faker->unique()->userName(),
            'father_name' => $this->faker->name(),
            'cnic' => $this->faker->numerify('#############'),
            'city_id' => $this->faker->numerify('#'),
            'phone_number' => $this->faker->phoneNumber(),
            'email'=> $this->faker->unique()->safeEmail(),
            'otp' => generateRandomString(4),
        ];
    }
}
