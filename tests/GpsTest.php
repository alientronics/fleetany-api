<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Entities\Gps;

class GpsTest extends TestCase
{
    public function testGpsDeleteFail()
    {
        $this->delete('/api/v1/gps');

        $this->assertEquals($this->response->status(), 405);

    }

    public function testGpsPostFail()
    {
        $this->post('/api/v1/gps', ['vehicle_id' => 1, 
                'json' => '{\"latitude\":51.10,\"longitude\":30.05,\"accuracy\":22.0,'
                            .'\"altitude\":1.10,\"altitudeAccuracy\":50.05,'
                            .'\"heading\":42.4,\"speed\":81.95}'
        ]);

        $this->assertEquals($this->response->status(), 401);

    }

    public function testGpsPostSuccess()
    {
        $company = factory('App\Company')->create();

        $this->actingAs($company)
            ->post('/api/v1/gps', ['api_token' => env('APP_TOKEN'), 
                'email' => 'admin@alientronics.com.br', 
                'vehicle_id' => 1, 
                'json' => '{\"latitude\":51.10,\"longitude\":30.05,\"accuracy\":22.0,'
                            .'\"altitude\":1.10,\"altitudeAccuracy\":50.05,'
                            .'\"heading\":42.4,\"speed\":81.95}'
            ])
            ->seeJson([
                'success' => true
            ]);

        $this->seeInDatabase('gps', ['vehicle_id' => 1, 
                                    'latitude' => 51.10, 
                                    'longitude' => 30.05, 
                                    'accuracy' => 22.0, 
                                    'altitude' => 1.10, 
                                    'altitudeAccuracy' => 50.05, 
                                    'heading' => 42.4, 
                                    'speed' => 81.95
        ]);
    }
}
