<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default OCR Provider
    |--------------------------------------------------------------------------
    |
    | Supported: "tesseract", "google_vision", "aws_textract"
    |
    */

    'default' => env('OCR_PROVIDER', 'tesseract'),

    /*
    |--------------------------------------------------------------------------
    | OCR Provider Configurations
    |--------------------------------------------------------------------------
    */

    'providers' => [
        'tesseract' => [
            'path' => env('TESSERACT_PATH', 'tesseract'),
            'language' => env('TESSERACT_LANGUAGE', 'eng+spa'),
            'psm' => env('TESSERACT_PSM', 3),
            'oem' => env('TESSERACT_OEM', 3),
        ],

        'google_vision' => [
            'credentials' => env('GOOGLE_CLOUD_KEY_FILE'),
            'project_id' => env('GOOGLE_CLOUD_PROJECT'),
        ],

        'aws_textract' => [
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Processing Options
    |--------------------------------------------------------------------------
    */

    'timeout' => env('OCR_TIMEOUT', 120), // seconds
    'max_file_size' => env('OCR_MAX_FILE_SIZE', 10485760), // 10MB
    'supported_formats' => ['jpg', 'jpeg', 'png', 'pdf', 'tiff', 'bmp'],
];
