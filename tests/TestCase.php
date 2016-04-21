<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class TestCase extends Laravel\Lumen\Testing\TestCase
{

    use DatabaseTransactions;
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
