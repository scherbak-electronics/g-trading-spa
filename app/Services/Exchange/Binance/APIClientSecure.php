<?php
namespace App\Services\Exchange\Binance;

use Binance\APIClient;

class APIClientSecure extends APIClient
{
    public function __construct($config)
    {
        parent::__construct($config);
    }

    public function signRequest($method, $path, array $params = [])
    {
        $params['recvWindow'] = 60000;
        return parent::signRequest($method, $path, $params);
    }
}
