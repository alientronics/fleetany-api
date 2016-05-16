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
                'longitude' => 30.05
            ])
            ->seeJson([
                'success' => true
            ]);

        $this->seeInDatabase('gps', ['vehicle_id' => 1, 
                                    'latitude' => 51.10, 
                                    'longitude' => 30.05
        ]);
    }

    public function testGpsExtraDataPostSuccess()
    {
        $company = factory('App\Company')->create();

        $this->actingAs($company)
            ->post('/api/v1/gps', ['api_token' => env('APP_TOKEN'), 
                'email' => 'admin@alientronics.com.br', 
                'vehicle_id' => 1, 
                'latitude' => 51.10, 
                'longitude' => 30.05,
                'json' => '{\"id\":0000000001,\"pr\":127,\"tp\":22,\"ba\":2.95}\r\n'
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
