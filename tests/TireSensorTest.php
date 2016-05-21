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
                'json' => '[{"id":"0000000001","pr":127,"tp":22.0,"ba":2.95'
                            .',"latitude":51.10,"longitude":30.05}]'
            ])
            ->seeJson([
                'success' => true
            ]);

        $this->seeInDatabase('tiresensor', ['vehicle_id' => 1, 
                                    'latitude' => 51.10, 
                                    'longitude' => 30.05, 
                                    'temperature' => 22.0, 
                                    'pressure' => 127, 
                                    'battery' => 2.95, 
                                    'number' => '0000000001'
        ]);
    }
}
