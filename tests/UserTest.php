<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class CompanyTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testPingApi()
    {
        $this->get('/');

        $this->assertEquals(
            $this->response->getContent(), $this->app->version()
        );
    }

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
        $this->put('/api/v1/user/testeapi@alientronics.com.br');

        $this->assertEquals($this->response->status(), 401);

    }

    public function testUserPostSuccess()
    {

        $company = factory('App\Company')->create();

        $this->actingAs($company)
            ->put('/api/v1/user/testeapi@alientronics.com.br')
            ->seeJson([
                'email' => 'testeapi@alientronics.com.br',
            ]);

        $this->seeInDatabase('users', ['email' => 'testeapi@alientronics.com.br']);
    }

}
