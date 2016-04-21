<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Entities\Company;

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

    public function testCompanyGetFail()
    {
        $this->get('/api/v1/company');

        $this->assertEquals($this->response->status(), 401);

    }

    public function testCompanyGetAllSuccess()
    {   
        $this->get('/api/v1/company', ['api_token' => env('APP_TOKEN')]);
        $this->assertEquals($this->response->status(), 200);
    }

    public function testCompanyGetSuccess()
    {
        $company = Company::find(1)->attributesToArray();
        unset($company['api_token']);
        unset($company['contact_id']);

        $this->get('/api/v1/company/1', ['api_token' => env('APP_TOKEN')])
            ->seeJson($company);
    }

    public function testCompanyDeleteFail()
    {
        $this->delete('/api/v1/company');

        $this->assertEquals($this->response->status(), 405);

    }

    public function testCompanyPostFail()
    {
        $this->put('/api/v1/company/1', ['name' => 'Sally Inc.']);

        $this->assertEquals($this->response->status(), 401);

    }

    public function testCompanyPostSuccess()
    {

        $company = factory('App\Company')->create();

        $this->actingAs($company)
            ->put('/api/v1/company/1', ['name' => 'Sally Inc.'])
            ->seeJson([
                'name' => 'Sally Inc.',
            ]);

        $this->seeInDatabase('companies', ['name' => 'Sally Inc.']);
    }

}
