<?php

$configs = [];

$configs["app.name"] = "Joplin";

$configs['languages'] = [
    'es' => 'Spanish',
    'vi' => 'Vietnamese',
    'ar' => 'Arabic',
];

if (!empty(getenv('COCKPIT_SESSION_NAME'))){
    $configs['session.name'] = getenv('COCKPIT_SESSION_NAME');
}

if (!empty(getenv('COCKPIT_SALT'))){
    $configs['sec-key'] = getenv('COCKPIT_SALT');
}

if (!empty(getenv('COCKPIT_I18N'))){
    $configs['i18n'] = getenv('COCKPIT_I18N');
}

if (!empty(getenv('COCKPIT_DATABASE_SERVER'))){
    $configs['database'] = [
        "server"  => getenv('COCKPIT_DATABASE_SERVER'),
        "options" => ["db" => getenv('COCKPIT_DATABASE_NAME')]
    ];
}

if (!empty(getenv('COCKPIT_MAILER_FROM'))){
    $configs['mailer'] = [
        "from"      => getenv('COCKPIT_MAILER_FROM'),
        "transport" => getenv('COCKPIT_MAILER_TRANSPORT'),
        "host"      => getenv('COCKPIT_MAILER_HOST'),
        "user"      => getenv('COCKPIT_MAILER_USER'),
        "password"  => getenv('COCKPIT_MAILER_PASSWORD'),
        "port"      => getenv('COCKPIT_MAILER_PORT'),
        "auth"      => getenv('COCKPIT_MAILER_AUTH'),
        "encryption"=> getenv('COCKPIT_MAILER_ENCRYPTION')
    ];
}

/*
cloudstorage:
    assets:
        type: s3
        key: xxxKeyxxx
        secret: xxxSecretxxx
        region: eu-central-1
        bucket: mybucket

        # optional
        endpoint: https://eu-central-1.amazonaws.com
        prefix: subfolder-name
        url: https://s3.eu-central-1.amazonaws.com
*/
$configs["cloudstorage"] = [
    "assets" => [
        "type" => "s3",
        "key" => getenv('COCKPIT_S3_STORAGE_KEY'),
        "secret" => getenv('COCKPIT_S3_STORAGE_SECRET'),
        "region" => getenv('COCKPIT_S3_STORAGE_REGION'),
        "bucket" => getenv('COCKPIT_S3_STORAGE_BUCKET'),
        "prefix" => getenv('COCKPIT_S3_STORAGE_PREFIX'),
    ]
];

/*
detektivo:
    engine: elasticsearch
    hosts: [http://localhost:32769]
    index: cockpit

$configs["detektivo"] = [
    "engine" => "elasticsearch",
    "hosts" => getenv('COCKPIT_ELASTICSEARCH_HOSTS'),
    "index" => getenv('COCKPIT_ELASTICSEARCH_INDEX'),
];
*/
return $configs;