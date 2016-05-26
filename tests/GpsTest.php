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
                'json' => '[{"latitude":51.10,"longitude":30.05,"accuracy":22.0,'
                            .'"altitude":1.10,"altitudeAccuracy":50.05,'
                            .'"heading":42.4,"speed":81.95}]'
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
                'dataIsCompressed' => 0, 
                'json' => '[{"latitude":51.10,"longitude":30.05,"accuracy":22.0,'
                            .'"altitude":1.10,"altitudeAccuracy":50.05,'
                            .'"heading":42.4,"speed":81.95}]'
            ])
            ->assertResponseStatus(200);

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

    public function testGpsPostSuccessDataIsCompressed()
    {
        $company = factory('App\Company')->create();

        $this->actingAs($company)
            ->post('/api/v1/gps', ['api_token' => env('APP_TOKEN'), 
                'email' => 'admin@alientronics.com.br', 
                'vehicle_id' => 1, 
                'dataIsCompressed' => 1, 
                'json' => 'UEsDBAoAAAAIAEkXukgf2863bQAAAGYBAAANAAAAcG9zdERhdGEuanN'.
                            'vbouuVkpMTi4tSkyuVLIyMdVRSswpySwpTUlVssorzclB8B3hqiDi'.
                            'GamJKZl56TBuTiJMm6mhnqEBUCA/Lx0qYmygZwA0ubggNTUFor5WB'.
                            '8VaS2pYa6pnimYtUASftSDllFtrqWeJZi1QBM3aWABQSwECFAAKAA'.
                            'AACABJF7pIH9vOt20AAABmAQAADQAAAAAAAAAAAAAAAAAAAAAAcG9'.
                            'zdERhdGEuanNvblBLBQYAAAAAAQABADsAAACYAAAAAAA='
            ])
            ->assertResponseStatus(200);
           
        $this->seeInDatabase('gps', ['vehicle_id' => 1, 
                                    'latitude' => 51.10, 
                                    'longitude' => 30.05, 
                                    'accuracy' => 45
        ]);  
        
        $this->seeInDatabase('gps', ['vehicle_id' => 1, 
                                    'latitude' => 55.50, 
                                    'longitude' => 35.55, 
                                    'accuracy' => 49
        ]);
        
        $this->seeInDatabase('gps', ['vehicle_id' => 1, 
                                    'latitude' => 59.90, 
                                    'longitude' => 39.95, 
                                    'accuracy' => 50
        ]);
    }
}
