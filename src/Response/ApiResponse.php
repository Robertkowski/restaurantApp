<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse extends JsonResponse
{

    public function __construct($data = null, $status = 200, $headers = [])
    {
        $currentDate = new \DateTime();
        $headers = array_merge(
            [
                'Content-Type' => 'application/json',
                'Server-Time' => $currentDate->format('Y-m-d H:i:s O')
            ],
            $headers
        );

        if (!$this->isJson($data)) {
            parent::__construct($data, $status, $headers);
        } else {
            Response::__construct($data, $status, $headers);
        }
    }

    protected function isJson($string)
    {
        if (!is_string($string)) {
            return false;
        }
        json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);
    }

}