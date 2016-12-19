<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Entities\TireSensor;
use App\Http\Controllers\AlertController;

class AlertTest extends TestCase
{
    public function testHasEntry()
    {
        $company = factory('App\Company')->create();
        $vehicle = factory('App\Entities\Vehicle')->create();
        $tireSensor = factory('App\Entities\TireSensor')->create();
        $user = factory('App\Entities\User')->create();
        
        $objAlertController = new AlertController();
        $return = $objAlertController->sendAlertTireMail($company, $vehicle->id, 
            $tireSensor, 100, [$user]);

        $this->assertEquals($return, true);
        
    }
}
