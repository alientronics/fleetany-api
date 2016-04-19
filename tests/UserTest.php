<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Entities\User;

class UserTest extends TestCase
{
    public function testUserGetFail()
    {
        $this->get('/api/v1/user');

        $this->assertEquals($this->response->status(), 401);

    }

    public function testUserDeleteFail()
    {
        $this->delete('/api/v1/user');

        $this->assertEquals($this->response->status(), 405);

    }

    public function testUserPostFail()
    {
        $this->post('/api/v1/user');

        $this->assertEquals($this->response->status(), 401);

    }

    public function testUserPostSuccess()
    {
        $user = User::where('email', 'admin@alientronics.com.br')->first();
        $vehicles = $user->company->vehicles;
        $vehicles = $vehicles->toArray();
        $vehicles = $vehicles[0];
        
        $this->post('/api/v1/user', ['api_token' => 'OTscjZ19F', 'email' => 'admin@alientronics.com.br'])
            ->seeJson($vehicles);

        $this->seeInDatabase('users', ['email' => 'admin@alientronics.com.br']);
    }

}
