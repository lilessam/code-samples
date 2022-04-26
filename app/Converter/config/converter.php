<?php

return [
    'default' => 'jbc',
    'drivers' => [
        "BSS",
        "COR",
        "FTC",
        "JBC",
        "KAP",
        "KAS",
        "MLK",
        "REM",
        "RRL",
        "SLI",
        "SSS",
        "SWS",
        "TDE",
    ],
    'default_date_format' => 'm-d-y',
    'callbacks' => [
        'customer_name' => function ($name) {
            return ucwords(strtolower($name));
        },
        'city' => function ($city) {
            return ucwords(strtolower($city));
        },
        'commission_rate' => function ($rate) {
            return number_format((float) $rate, 2, '.', '') . '%';
        },
        'calculable' => function ($sales) {
            return (float) number_format((float) $sales, 2, '.', '');
        }
    ]
];
