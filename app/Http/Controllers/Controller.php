<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected function getZipContent($base64)
    {
        $deflate = base64_decode($base64);
        preg_match('/postData.json(.*)PK/', $deflate, $matches);
        if (!empty($matches[1])) {
            return gzinflate($matches[1]);
        } else {
            return null;
        }
    }
}
