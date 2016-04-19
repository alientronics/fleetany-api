<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class TripTest extends TestCase
{
    public function testTripGetFail()
    {
        $this->get('/api/v1/trip');

        $this->assertEquals($this->response->status(), 401);

    }

    public function testTripDeleteFail()
    {
        $this->delete('/api/v1/trip');

        $this->assertEquals($this->response->status(), 405);

    }

    public function testTripPostFail()
    {
        $this->post('/api/v1/trip', ['api_token' => 'OTscjZ19F', 
                                    'email' => 'admin@alientronics.com.br', 
                                    'vehicle_id' => 1, 
                                    'fuel_cost' => 51.10, 
                                    'fuel_amount' => 30.05, 
                                    'end_mileage' => 123, 
                                    'fuel_type' => 2, 
                                    'tank_fill_up' => 0
        ]);

        $this->assertEquals($this->response->status(), 401);

    }

    public function testTripPostSuccess()
    {
        $company = factory('App\Company')->create();

        $this->actingAs($company)
            ->post('/api/v1/trip', ['api_token' => 'OTscjZ19F', 
                                    'email' => 'admin@alientronics.com.br', 
                                    'vehicle_id' => 1, 
                                    'fuel_cost' => 51.10, 
                                    'fuel_amount' => 30.05, 
                                    'end_mileage' => 123, 
                                    'fuel_type' => 2, 
                                    'tank_fill_up' => 0
            ])
            ->seeJson([
                'success' => true
            ]);

        $this->seeInDatabase('trips', ['vehicle_id' => 1, 
                                    'fuel_cost' => 51.10, 
                                    'fuel_amount' => 30.05, 
                                    'end_mileage' => 123, 
                                    'fuel_type' => 2, 
                                    'tank_fill_up' => 0
        ]);
    }

}
