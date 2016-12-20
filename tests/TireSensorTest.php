<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Entities\TireSensor;
use App\Entities\Type;

class TireSensorTest extends TestCase
{
    private function setEloquentMock($method, $return)
    {
        $mockRepo = \Mockery::mock('App\Http\Controllers\TireConditionController');
        $mockRepo->shouldReceive($method)->andReturn($return);
        $this->app->instance('App\Http\Controllers\TireConditionController', $mockRepo);
    }
    
    public function testTireSensorDeleteFail()
    {
        $this->delete('/api/v1/tiresensor');

        $this->assertEquals($this->response->status(), 405);

    }

    public function testTireSensorPostFail()
    {
        $this->setEloquentMock('checkTireCondition', true);
        $this->post('/api/v1/tiresensor', ['vehicle_id' => 1, 
                'json' => '[{"id":"0000000001","pr":127,"pos":2,"tp":22.0,"ba":2.95'
                            .',"latitude":51.10,"longitude":30.05}]'
        ]);

        $this->assertEquals($this->response->status(), 401);

    }

    public function testTireSensorPostSuccess()
    {
        $company = factory('App\Company')->create();

        //$this->setEloquentMock('checkTireCondition', true);
        $this->actingAs($company)
            ->post('/api/v1/tiresensor', ['api_token' => env('APP_TOKEN'), 
                'email' => 'admin@alientronics.com.br', 
                'vehicle_id' => 1, 
                'dataIsCompressed' => 0,
                'json' => '[{"id":"0000000001","pr":127,"pos":2,"tp":22.0,"ba":2.95'
                            .',"latitude":51.10,"longitude":30.05}]'
            ]);
//             ->assertResponseStatus(200);
        echo $this->response->getContent();

        $this->seeInDatabase('tire_sensor', ['latitude' => 51.10, 
                                    'longitude' => 30.05, 
                                    'temperature' => 22.0, 
                                    'pressure' => 127, 
                                    'battery' => 2.95, 
                                    'number' => '0000000001'
        ]);
    }

    public function testTireSensorGenerateEntry()
    {
        $company = factory('App\Company')->create();
        $entry_type = factory('App\Entities\Type')->create();
        
        $this->actingAs($company)
            ->post('/api/v1/tiresensor', ['api_token' => env('APP_TOKEN'),
                'email' => 'admin@alientronics.com.br',
                'vehicle_id' => 1,
                'dataIsCompressed' => 0,
                'json' => '[{"id":"0000000001","pr":100,"pos":2,"tp":122.0,"ba":2.95'
                .',"latitude":51.10,"longitude":30.05}]'
            ]);            

    }

    public function testTireSensorPostSuccessDataIsCompressed()
    {
        $company = factory('App\Company')->create();

        $this->setEloquentMock('checkTireCondition', true);
        $this->actingAs($company)
            ->post('/api/v1/tiresensor', ['api_token' => env('APP_TOKEN'), 
                'email' => 'admin@alientronics.com.br', 
                'vehicle_id' => 1, 
                'dataIsCompressed' => 1,
                'json' => 'UEsDBAoAAAAIAN0L+Ujj/CdFYQAAALkAAAANAAAAcG9zdERhdGEuanNvbouuVspMUb'.
                            'JSMoABQyUdpYIiJStDI3MgI79YycpIR6mkAEgZ6RnoKCUlAll6lqY6SjmJJZklpS'.
                            'mpSlamhnqGQKmc/Lx0qIixgZ6Baa0OutlGcLMtoGYbQ802hpltTKTZsQBQSwECFA'.
                            'AKAAAACADdC/lI4/wnRWEAAAC5AAAADQAAAAAAAAAAAAAAAAAAAAAAcG9zdERhdG'.
                            'EuanNvblBLBQYAAAAAAQABADsAAACMAAAAAAA='
            ])
            ->assertResponseStatus(200);

        $this->seeInDatabase('tire_sensor', ['temperature' => 22.0, 
                                    'pressure' => 127, 
                                    'battery' => 2.95, 
                                    'number' => '0000000001'
        ]);

        $this->seeInDatabase('tire_sensor', ['temperature' => 23.0, 
                                    'pressure' => 128, 
                                    'battery' => 3.95, 
                                    'number' => '0000000002'
        ]);
    }
}
