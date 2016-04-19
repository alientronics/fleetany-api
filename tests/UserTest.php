<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

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
        $this->post('/api/v1/user', ['api_token' => 'OTscjZ19F', 'email' => 'testeapi@alientronics.com.br']);

        $this->assertEquals($this->response->status(), 401);

    }

    public function testUserPostSuccess()
    {
        $this->post('/api/v1/user', ['api_token' => 'OTscjZ19F', 'email' => 'testeapi@alientronics.com.br'])
            ->seeJson([
                'email' => 'testeapi@alientronics.com.br',
            ]);

        $this->seeInDatabase('users', ['email' => 'testeapi@alientronics.com.br']);
    }

}
