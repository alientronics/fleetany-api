<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Entities\Trip;

class TripTest extends TestCase
{
    public function testTripGetFail()
    {
        $this->get('/api/v1/trip');

        $this->assertEquals($this->response->status(), 401);

    }
    
    public function testTripGetSuccess()
    {
        $trips = Trip::all();
        $trips = $trips->toArray();
        
        $this->get('/api/v1/trip', ['api_token' => env('APP_TOKEN')])
            ->seeJson($trips);
    }

    public function testTripDeleteFail()
    {
        $this->delete('/api/v1/trip');

        $this->assertEquals($this->response->status(), 405);

    }

    public function testTripPostFail()
    {
        $this->post('/api/v1/trip', ['vehicle_id' => 1, 
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
            ->post('/api/v1/trip', ['api_token' => env('APP_TOKEN'), 
                                    'email' => 'admin@alientronics.com.br', 
                                    'vehicle_id' => 1, 
                                    'fuel_cost' => 51.10, 
                                    'fuel_amount' => 30.05, 
                                    'end_mileage' => 123, 
                                    'fuel_type' => 2, 
                                    'tank_fill_up' => 0
            ])
            ->assertResponseStatus(200);

        $this->seeInDatabase('trips', ['vehicle_id' => 1, 
                                    'fuel_cost' => 51.10, 
                                    'fuel_amount' => 30.05, 
                                    'end_mileage' => 123, 
                                    'fuel_type' => 2, 
                                    'tank_fill_up' => 0
        ]);
    }

}
