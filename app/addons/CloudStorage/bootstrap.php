<?php

// autoload vendor libs
include(__DIR__.'/vendor/autoload.php');


$this->on('cockpit.filestorages.init', function(&$storages) {

    $config = $this->retrieve('config/cloudstorage');

    if (!$config) {
        return;
    }

    foreach ($config as $key => $settings) {

        $type = isset($settings['type']) ? $settings['type'] : 's3';

        if (!isset($settings['prefix'])) {
            $settings['prefix'] = '';
        }

        switch ($type) {

            case 's3':

                if (!isset($settings['key'], $settings['secret'], $settings['region'], $settings['bucket'])) {
                    break;
                }

                $url = $settings['url'] ?? 'https://s3.'.$settings['region'].'.amazonaws.com/'.$settings['bucket'];

                if (!isset($settings['url']) && $settings['prefix']) {
                    $url = "{$url}/{$settings['prefix']}";
                }

                $client = new Aws\S3\S3Client(array_merge(
                    [
                        'credentials' => [
                            'key'    => $settings['key'],
                            'secret' => $settings['secret']
                        ],
                        'region'  => $settings['region'],
                        'version' => isset($settings['version']) ? $settings['version'] : 'latest',
                    ],
                    $settings['endpoint'] ? ['endpoint' => $settings['endpoint']] : []
                ));

                $storages[$key] = [
                    'adapter' => 'League\Flysystem\AwsS3v3\AwsS3Adapter',
                    'args'    => [$client, $settings['bucket'], $settings['prefix']],
                    'mount'   => true,
                    'url'     => $url
                ];

                break;


            case 'azure':

                if (!isset($settings['key'], $settings['account'], $settings['container'])) {
                    break;
                }

                $endpoint = sprintf('DefaultEndpointsProtocol=https;AccountName=%s;AccountKey=%s', $settings['account'], $settings['key']);
                $blobRestProxy = MicrosoftAzure\Storage\Common\ServicesBuilder::getInstance()->createBlobService($endpoint);

                $url = $settings['url'] ?? 'https://'.$settings['account'].'.blob.core.windows.net/'.$settings['container'];

                if (!isset($settings['url']) && $settings['prefix']) {
                    $url = "{$url}/{$settings['prefix']}";
                }

                $storages[$key] = [
                    'adapter' => 'League\Flysystem\Azure\AzureAdapter',
                    'args'    => [$blobRestProxy, $settings['container'], $settings['prefix']],
                    'mount'   => true,
                    'url'     => $url
                ];

                break;

            case 'minio':

                if (!isset($settings['key'], $settings['secret'], $settings['bucket'])) {
                    break;
                }

                if (!isset($settings['region'])) {
                    $settings['region'] = 'us-east-1';
                }

                $endpoint = $settings['endpoint'] ?? 'http://localhost:9000';

                $image_address = $settings['image_address'] ?? $endpoint."/".$settings['bucket'];

                if (!isset($settings['image_address']) && $settings['prefix']) {
                    $image_address = "{$image_address}/{$settings['prefix']}";
                }

                $client = new Aws\S3\S3Client([
                    'credentials' => [
                        'key'    => $settings['key'],
                        'secret' => $settings['secret']
                    ],
                    'use_path_style_endpoint' => true,
                    'endpoint'                => $endpoint,
                    'region'                  => $settings['region'],
                    'version'                 => isset($settings['version']) ? $settings['version'] : 'latest',
                ]);

                $storages[$key] = [
                    'adapter' => 'League\Flysystem\AwsS3v3\AwsS3Adapter',
                    'args'    => [$client, $settings['bucket'], $settings['prefix']],
                    'mount'   => true,
                    'url'     => $image_address
                ];

                break;
        }

    }
});
