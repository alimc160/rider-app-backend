<?php

return [
    'operations' => [
        'pending' => [
            'content' => 'pending',
            'text_color' =>  '#CF8730',
            'bg_color' => '#CF873033'
        ],
        'approved' => [
            'content' => 'approved',
            'text_color' =>  '#1FC54D',
            'bg_color' => '#1FC44D33'
        ],
        'cancelled' => [
            'content' => 'cancelled',
            'text_color' =>  '#fff',
            'bg_color' => '#dc3545'
        ],
        'in_progress' => [
            'content' => 'in_progress',
            'text_color' =>  '#CF8730',
            'bg_color' => '#CF873033'
        ]
    ],
    'booking_order_statues' => [
        'pending',
        'accepted',
        'arrived_for_pickup',
        'ride_started',
//        'package_collected',
        'arrived_for_drop_off',
        'package_delivered'
    ]
];
