<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Aws\S3\S3Client;
use Aws\S3\PostObjectV4;
use App\Service\ParameterStore;

class ApiController extends AbstractController
{
    public function getPresignedUrl(S3Client $s3Client, ParameterStore $store, Request $request): Response
    {
        $cmd = $s3Client->getCommand('GetObject', [
            'Bucket' => $store->get('app_bucket_name'),
            'Key' => $request->request->get('bucket_key'),
        ]);
        $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');
        $presignedUrl = (string) $request->getUri();
        return $this->json(['presignedUrl' => $presignedUrl]);
    }

    public function getPresignedUploadUrl(S3Client $s3Client, ParameterStore $store, Request $request): Response
    {
        $bucket = $store->get('app_bucket_name');
        $formInputs = ['acl' => 'public-read'];
        $options = [
            ['acl' => 'public-read'],
            ['bucket' => $bucket],
            ['starts-with', '$key', 'test/'],
        ];
        $expires = '+2 hours';

        $postObject = new PostObjectV4(
            $s3Client,
            $bucket,
            $formInputs,
            $options,
            $expires
        );

        $formAttributes = $postObject->getFormAttributes();
        $formInputs = $postObject->getFormInputs();

        return $this->json([
            'formAttributes' => $formAttributes,
            'formInputs' => $formInputs,
        ]);
    }
}