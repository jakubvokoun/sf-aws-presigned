<?php

namespace App\Service;

use Exception;
use Aws\Ssm\SsmClient;

class ParameterStore
{
    /** @var SsmClient */
    private $client;

    public function __construct(SsmClient $client)
    {
        $this->client = $client;
    }

    public function get($key)
    {
        $result = $this->client->getParameter(['Name' => $key]);
        if ($result->hasKey('Parameter')) {
            return $result->get('Parameter')['Value'];
        }
        throw new Exception(sprintf('Parameter "%s" not found', $key));
    }
}