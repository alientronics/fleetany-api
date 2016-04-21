<?php

class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    public function get($uri, array $headers = [])
    {
    	$params = "";
    	foreach ($headers as $key => $value) {
    		$params .= "$key=$value&";
    	}
    	return parent::get($uri.'?'.$params);
    }
}
