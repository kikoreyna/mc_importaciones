<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$s3 = new S3Client([
    'version' => 'latest',
    'region'  => 'us-east-2',
    'credentials' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
    ],
]);

try {
    $result = $s3->putObject([
        'Bucket' => 'mcimportaciones',
        'Key'    => 'prueba/test.txt',
        'Body'   => 'hola desde aws',
    ]);
    echo "EXITO: " . $result['ObjectURL'] . "\n";
} catch (AwsException $e) {
    echo "ERROR AWS: " . $e->getAwsErrorCode() . "\n";
    echo "Mensaje: " . $e->getMessage() . "\n";
    echo "HTTP Status: " . $e->getStatusCode() . "\n";
}
