<?php

use Aws\Laravel\AwsServiceProvider;

return [
    'credentials' => [
        'key'    => env('s3_key'),
        'secret' => env('s3_secret'),
    ],
    'region' => env('AWS_REGION','us-west-2'),
    'version' => 'latest',
];