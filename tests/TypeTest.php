<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Entities\Type;

class TypeTest extends TestCase
{
    public function testTypeFuelGetFail()
    {
        $this->get('/api/v1/fuelType');

        $this->assertEquals($this->response->status(), 401);

    }
    
    public function testTypeFuelGetSuccess()
    {
        $types = Type::where('entity_key', 'fuel')
                    ->where('company_id', 1)
                    ->get();
        $types = $types->toArray();
        
        $this->get('/api/v1/fuelType', ['api_token' => env('APP_TOKEN')])
            ->seeJson($types);
    }

}
