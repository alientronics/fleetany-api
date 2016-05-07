<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Entities\Gps;

class GpsTest extends TestCase
{
    public function testGpsGetFail()
    {
        $this->get('/api/v1/gps');

        $this->assertEquals($this->response->status(), 401);

    }
    
    public function testGpsGetSuccess()
    {
        $gps = Gps::all();
        $gps = $gps->toArray();
        
        $this->get('/api/v1/gps', ['api_token' => env('APP_TOKEN')])
            ->seeJson($gps);
    }

    public function testGpsDeleteFail()
    {
        $this->delete('/api/v1/gps');

        $this->assertEquals($this->response->status(), 405);

    }

    public function testGpsPostFail()
    {
        $this->post('/api/v1/gps', ['vehicle_id' => 1, 
                                    'latitude' => 51.10, 
                                    'longitude' => 30.05
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
                'latitude' => 51.10, 
                'longitude' => 30.05,
                'json' => '{"json": 
                            [
                            { "id": "123456", "ts": "2016-01-02 10:13:14", "temp": "70.25", "press": "30.45" },
                            { "id": "123457", "ts": "2016-03-04 11:13:14", "temp": "80.26", "press": "40.46" },
                            { "id": "123458", "ts": "2016-05-06 12:13:14", "temp": "90.27", "press": "50.47" },
                            ]
                            }'
            ])
            ->seeJson([
                'success' => true
            ]);

        $this->seeInDatabase('gps', ['vehicle_id' => 1, 
                                    'latitude' => 51.10, 
                                    'longitude' => 30.05
        ]);
    }

}
