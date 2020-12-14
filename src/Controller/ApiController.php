<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Aws\S3\S3Client;
use Aws\S3\PostObjectV4;

class ApiController extends AbstractController
{
    public function getPresignedUrl(S3Client $s3Client, Request $request): Response
    {
        $cmd = $s3Client->getCommand('GetObject', [
            'Bucket' => $this->getParameter('app.s3_bucket'),
            'Key' => $request->request->get('bucket_key'),
        ]);
        $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');
        $presignedUrl = (string) $request->getUri();
        return $this->json(['presignedUrl' => $presignedUrl]);
    }

    public function getPresignedUploadUrl(S3Client $s3Client, Request $request): Response
    {
        $bucket = $this->getParameter('app.s3_bucket');
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