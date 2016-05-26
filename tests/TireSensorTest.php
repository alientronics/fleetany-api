<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Entities\TireSensor;

class TireSensorTest extends TestCase
{
    public function testTireSensorDeleteFail()
    {
        $this->delete('/api/v1/tiresensor');

        $this->assertEquals($this->response->status(), 405);

    }

    public function testTireSensorPostFail()
    {
        $this->post('/api/v1/tiresensor', ['vehicle_id' => 1, 
                'json' => '[{"id":"0000000001","pr":127,"tp":22.0,"ba":2.95'
                            .',"latitude":51.10,"longitude":30.05}]'
        ]);

        $this->assertEquals($this->response->status(), 401);

    }

    public function testTireSensorPostSuccess()
    {
        $company = factory('App\Company')->create();

        $this->actingAs($company)
            ->post('/api/v1/tiresensor', ['api_token' => env('APP_TOKEN'), 
                'email' => 'admin@alientronics.com.br', 
                'vehicle_id' => 1, 
                'dataIsCompressed' => 0,
                'json' => '[{"id":"0000000001","pr":127,"tp":22.0,"ba":2.95'
                            .',"latitude":51.10,"longitude":30.05}]'
            ])
            ->seeJson([
                'success' => true
            ]);

        $this->seeInDatabase('tire_sensor', ['latitude' => 51.10, 
                                    'longitude' => 30.05, 
                                    'temperature' => 22.0, 
                                    'pressure' => 127, 
                                    'battery' => 2.95, 
                                    'number' => '0000000001'
        ]);
    }

    public function testTireSensorPostSuccessDataIsCompressed()
    {
        $company = factory('App\Company')->create();

        $this->actingAs($company)
            ->post('/api/v1/tiresensor', ['api_token' => env('APP_TOKEN'), 
                'email' => 'admin@alientronics.com.br', 
                'vehicle_id' => 1, 
                'dataIsCompressed' => 1,
                'json' => 'UEsDBAoAAAAIALsZukhiAMKlXgAAAPEAAAANAAAAcG9zdERhdGEuanNvbouuVspMUb'.
                            'JSMoABQyUdpYIiJStDI3MdpZICJSsjIx2lpEQgrWdpqqOUk1iSWVKakqpklVeakw'.
                            'Pk5+elIwnU6qCbZwQ3zwJqnjHEPGPyzDOGm2cJNc8EYp4JUebFAgBQSwECFAAKAA'.
                            'AACAC7GbpIYgDCpV4AAADxAAAADQAAAAAAAAAAAAAAAAAAAAAAcG9zdERhdGEuan'.
                            'NvblBLBQYAAAAAAQABADsAAACJAAAAAAA='
            ])
            ->seeJson([
                'success' => true
            ]);

        $this->seeInDatabase('tire_sensor', ['latitude' => 51.10, 
                                    'longitude' => 30.05, 
                                    'temperature' => 22.0, 
                                    'pressure' => 127, 
                                    'battery' => 2.95, 
                                    'number' => '0000000001'
        ]);

        $this->seeInDatabase('tire_sensor', ['latitude' => 55.50, 
                                    'longitude' => 35.55, 
                                    'temperature' => 23.0, 
                                    'pressure' => 128, 
                                    'battery' => 3.95, 
                                    'number' => '0000000002'
        ]);

        $this->seeInDatabase('tire_sensor', ['latitude' => 59.90, 
                                    'longitude' => 39.95, 
                                    'temperature' => 24.0, 
                                    'pressure' => 129, 
                                    'battery' => 4.95, 
                                    'number' => '0000000003'
        ]);
    }
}
