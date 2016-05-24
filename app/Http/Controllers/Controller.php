<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected function getZipContent($data)
    {
        if (base64_encode(base64_decode($data, true)) === $data) {
            $data = base64_decode($data);
        }
        $data = strstr($data, '[{');
        $data = strstr($data, '}]', true)."}]";
        return $data;
    }
}
