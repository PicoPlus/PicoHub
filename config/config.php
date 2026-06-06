<?php

return [
    'app' => [
        'name' => 'PicoPlus',
        'debug' => filter_var($_ENV['APP_DEBUG'] ?? 'false', FILTER_VALIDATE_BOOLEAN),
        'url' => rtrim($_ENV['APP_URL'] ?? '', '/'),
    ],

    'hubspot' => [
        'token' => $_ENV['HUBSPOT_TOKEN'] ?? '',
        'base_url' => 'https://api.hubapi.com',
        'log_channel' => $_ENV['HUBSPOT_LOG_CHANNEL'] ?? 'hubspot',
    ],

    'zohal' => [
        'token' => $_ENV['ZOHAL_TOKEN'] ?? '',
        'base_url' => $_ENV['ZOHAL_BASE_URL'] ?? 'https://service.zohal.io/api',
    ],

    'ippanel' => [
        'base_url' => $_ENV['IPPANEL_BASE_URL'] ?? 'https://edge.ippanel.com/v1',
        'api_key' => $_ENV['IPPANEL_API_KEY'] ?? '',
        'from_number' => $_ENV['IPPANEL_FROM_NUMBER'] ?? '',
        'otp_method' => $_ENV['IPPANEL_OTP_METHOD'] ?? 'pattern',
        'otp_param' => $_ENV['IPPANEL_OTP_PARAM'] ?? 'code',
        'pattern_otp' => $_ENV['IPPANEL_PATTERN_OTP'] ?? '',
        'pattern_welcome' => $_ENV['IPPANEL_PATTERN_WELCOME'] ?? '',
        'pattern_deal_closed' => $_ENV['IPPANEL_PATTERN_DEAL_CLOSED'] ?? '',
    ],

    'admin_users' => [
        'admin@picoplus.app' => $_ENV['ADMIN_PASSWORD_DEFAULT'] ?? 'Admin@123',
        'secgen.unity@gmail.com' => $_ENV['ADMIN_PASSWORD_SECGEN'] ?? 'Secgen@2024',
        'manager@picoplus.app' => $_ENV['ADMIN_PASSWORD_MANAGER'] ?? 'Manager@123',
    ],

    'contact_properties' => [
        'firstname', 'lastname', 'email', 'phone', 'ncode', 'date_of_birth',
        'father_name', 'gender', 'total_revenue', 'shahkar_status', 'wallet',
        'num_associated_deals', 'contact_plan',
    ],

    'otp' => [
        'ttl_minutes' => 5,
        'max_attempts' => 3,
    ],

    'cache' => [
        'user_panel_ttl' => 300,
    ],
];
