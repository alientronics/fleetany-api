<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Log;
class Controller extends BaseController
{
    protected function getZipContent($zipString) {
        Log::info(print_r($zipString,1));
        $data = base64_decode($zipString);
        $data = strstr($data, '[{');
        $data = strstr($data, '}]', true)."}]";
        return $data;
    }
}
