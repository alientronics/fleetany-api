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
    
    public function testUserGetSuccess()
    {
        $user = User::find(1)->attributesToArray();
        
        $this->get('/api/v1/user', ['api_token' => env('APP_TOKEN')])
            ->seeJson($user);
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
        
        $this->post('/api/v1/user', ['api_token' => env('APP_TOKEN'), 'email' => 'admin@alientronics.com.br'])
            ->seeJson($vehicles);

        $this->seeInDatabase('users', ['email' => 'admin@alientronics.com.br']);
    }

    public function testUserPostCreateSuccess()
    {
        $this->post('/api/v1/user', ['api_token' => env('APP_TOKEN'), 'email' => 'admin2@alientronics.com.br'])
            ->seeJson();

        $this->seeInDatabase('users', ['email' => 'admin2@alientronics.com.br']);
    }

}
