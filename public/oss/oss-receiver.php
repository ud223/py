<?php

require ('../../extlib/OSS/aliyun.php');

use \Aliyun\OSS\OSSClient;

$client = OSSClient::factory(array(
            'AccessKeyId' => 'q4JANNcG8ki8hQRZ',
            'AccessKeySecret' => 'z0BkNBjRBUU0gsobVupXJY8yHGXkFm',
        ));

function putResourceObject(OSSClient $client, $bucket, $key, $content, $size) {
    $result = $client->putObject(array(
        'Bucket' => $bucket,
        'Key' => $key,
        'Content' => $content,
        'ContentLength' => $size,
    ));
    echo 'Put object etag: ' . $result->getETag();
}

$file = $_FILES["file"]["tmp_name"];
$filesize = $_FILES["file"]["size"];

$keyId = '8urD64ete7oe9tZn';
$keySecret = '5RY0EtXgmfYyCsOguzUalXgYJlka9x';
$bucket = 'angelhere-cheesetv';
$key = $_FILES["file"]["name"];

putResourceObject($client, $bucket, $key, fopen($file, 'r'), $filesize);
