<?php

require ('../../extlib/OSS/aliyun.php');

use \Aliyun\OSS\OSSClient;

$client = OSSClient::factory(array(
            'AccessKeyId' => '8urD64ete7oe9tZn',
            'AccessKeySecret' => '5RY0EtXgmfYyCsOguzUalXgYJlka9x',
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
$bucket = 'z2junjun';
$key = $_FILES["file"]["name"];

putResourceObject($client, $bucket, $key, fopen($file, 'r'), $filesize);
