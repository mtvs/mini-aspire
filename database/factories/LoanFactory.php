<?php

use Faker\Generator as Faker;

$factory->define(App\Loan::class, function (Faker $faker) {
    return [
        'officer_id' => function () {
            return factory(\App\User::class)->state('officer')->create();
        },
        'amount' => $faker->numberBetween(10000),
        'duration' => $faker->numberBetween(3, 10 * 12),
        'interest_rate' => $faker->numberBetween(5, 20),
        'arrangement_fee' => $faker->numberBetween(0, 1000),
        'customer_id' => function () {
            return factory(\App\User::class)->create();
        }
    ];
});
