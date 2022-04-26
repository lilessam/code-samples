<?php

return [
    'authentication' => [
        'credentials' => [
            'devId' => '8cd312ff-5468-4b6f-acad-17171045c88a',//'8cd312ff-5468-4b6f-acad-17171045c88a',
            'appId' => 'AvanSabe-ZapERP-PRD-bc22ddb1d-d4736272',//'AvanSabe-ZapERP-SBX-accf1173b-51e93007',
            'certId' => 'PRD-c22ddb1d41e5-f8b9-4d9b-9f29-4c7c',//'SBX-ccf1173b3211-5a0a-4302-b51b-b944',
        ],
        'ruName' => 'AvanSaber_Inc-AvanSabe-ZapERP-gmmrhxg',//'AvanSaber_Inc-AvanSabe-ZapERP-gtojwosz',
        'ServerUrl' => 'https://api.ebay.com/ws/api.dll',//'https://api.sandbox.ebay.com/ws/api.dll',
        'UserToken' => 'ebayProductionUserToken',//'ebaySandboxUserToken',
        'sandbox' => false
    ],
    'scopes' => [
        'https://api.ebay.com/oauth/api_scope/sell.account',
        'https://api.ebay.com/oauth/api_scope/sell.inventory'
    ]
];
