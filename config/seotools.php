<?php

return [
    'inertia' => env('SEO_TOOLS_INERTIA', false),

    'meta' => [
        'defaults' => [
            'title' => env('APP_NAME', 'InvestSMA'),
            'titleBefore' => false,
            'description' => env('APP_SEO_DESCRIPTION', 'Bienes raíces e inversión inmobiliaria en San Miguel de Allende.'),
            'separator' => ' | ',
            'keywords' => [],
            'canonical' => false,
            'robots' => false,
        ],

        'webmaster_tags' => [
            'google' => env('SEO_TAG_GOOGLE'),
            'bing' => env('SEO_TAG_BING'),
            'alexa' => env('SEO_TAG_ALEXA'),
            'pinterest' => env('SEO_TAG_PINTEREST'),
            'yandex' => env('SEO_TAG_YANDEX'),
            'norton' => env('SEO_TAG_NORTON'),
        ],

        'add_notranslate_class' => false,
    ],

    'opengraph' => [
        'defaults' => [
            'title' => env('APP_NAME', 'InvestSMA'),
            'description' => env('APP_SEO_DESCRIPTION', 'Bienes raíces e inversión inmobiliaria en San Miguel de Allende.'),
            'url' => false,
            'type' => 'website',
            'site_name' => env('APP_NAME', 'InvestSMA'),
            'images' => [],
        ],
    ],

    'twitter' => [
        'defaults' => [
            'card' => 'summary_large_image',
        ],
    ],

    'json-ld' => [
        'defaults' => [
            'title' => env('APP_NAME', 'InvestSMA'),
            'description' => env('APP_SEO_DESCRIPTION', 'Bienes raíces e inversión inmobiliaria en San Miguel de Allende.'),
            'url' => false,
            'type' => 'WebPage',
            'images' => [],
        ],
    ],
];
