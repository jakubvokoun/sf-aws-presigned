<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Aws\S3\S3Client;

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
}