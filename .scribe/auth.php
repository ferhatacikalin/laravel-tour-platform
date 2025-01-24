<?php

return [
    'auth' => [
        'enabled' => true,
        'in' => 'bearer',
        'name' => 'Authorization',
        'use_value' => 'Bearer {token}',
        'placeholder' => '{token}',
        'extra_info' => 'You can retrieve your token by logging in through the /api/v1/auth/login endpoint.'
    ],
]; 