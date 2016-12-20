<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Company::class, function ($faker) {
    return [
        'name' => $faker->name,
        'delta_pressure' => 10,
        'ideal_pressure' => 100,
        'limit_temperature' => 80,
        'api_token' => $faker->name,
    ];
});

$factory->define(App\Entities\User::class, function ($faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email
    ];
});

$factory->define(App\Entities\Vehicle::class, function ($faker) {
    return [
        'fleet' => $faker->name,
        'number' => $faker->randomDigit,
        'model_vehicle_id' => 1,
        'company_id' => 1,
    ];
});

$factory->define(App\Entities\Part::class, function ($faker) {
    return [
        'name' => $faker->name,
        'company_id' => 1,
    ];
});

$factory->define(App\Entities\Gps::class, function ($faker) {
    return [
        'latitude' => $faker->name,
        'longitude' => $faker->name,
        'company_id' => 1,
    ];
});

$factory->define(App\Entities\TireSensor::class, function ($faker) {
    return [
        'pressure' => 127,
        'temperature' => 60,
        'created_at' => "1990-01-01 00:00:00",
    ];
});

$factory->define(App\Entities\Type::class, function ($faker) {
    return [
        'entity_key' => 'entry',
        'company_id' => 1,
        'name' => 'calibration maintenance',
    ];
});
