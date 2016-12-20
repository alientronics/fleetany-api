<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Entities\User;
use App\Entities\TireSensor;
use App\Entities\Part;
use Illuminate\Support\Facades\Log;
use App\Entities\Type;
use App\Entities\Model;

class TireSensorController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
  
        $TireSensor = TireSensor::all();
  
        return response()->json($TireSensor);
    }
  
    public function create(Request $request)
    {
        $inputs = $request->all();
            
        if (!empty($inputs['json'])) {
            if (!empty($inputs['dataIsCompressed']) && $inputs['dataIsCompressed'] == 1) {
                $inputs['json'] = $this->getZipContent($inputs['json']);
            }
            $jsonData = json_decode($inputs['json'], true);
           
            $user = User::where('email', $inputs['email'])->first();

            foreach ($jsonData as $json) {
                if (isset($json['id']) && isset($json['tp']) && isset($json['pr']) && isset($json['pos'])) {
                    $part = $this->getPart($user, $json, $inputs);
    
                    $tireSensor = TireSensor::forceCreate(["latitude" => $this->validateNumeric($json['latitude']),
                        "longitude" => $this->validateNumeric($json['longitude']),
                        //"created_at" => ( $this->validateDate($json['ts']) ?
                        //    $json['ts'] : \DB::raw('NOW()') ),
                        "number" => $json['id'],
                        "temperature" => $this->validateNumeric($json['tp']),
                        "pressure" => $this->validateNumeric($json['pr']),
                        "battery" => $this->validateNumeric($json['ba']),
                        "part_id" => $this->validateNumeric($part->id)
                    ]);

                    $this->checkTireCondition($user, $tireSensor, $inputs);
                }
            }
        }
        
        Log::info('TireSensor Data: '.json_encode($inputs));
            
        return (new \Illuminate\Http\Response)->setStatusCode(200);
    }
    
    public function checkTireCondition($user, $tireSensor, $inputs)
    {
        try {
            $objTireCondition = new TireConditionController();
            $objTireCondition->checkTireCondition(
                $user->company_id,
                $tireSensor->id,
                $inputs['vehicle_id']
            );
        } catch (\Exception $e) {
            Log::info('Alert Error: '.$e->getMessage());
        }
    }
    
    private function getPart($user, $json, $inputs)
    {
echo $json['id'] . " - " . $user->company_id;
        $part = Part::select('id')->where('number', $json['id'])
            ->where('company_id', $user->company_id)
            ->first();
        
        if (empty($part)) {
            $tireType = Type::where('company_id', $user->company_id)
                ->where('name', 'tire')
                ->first();
             
            $tireModel = Model::where('company_id', $user->company_id)
                ->where('model_type_id', $tireType->id)
                ->first();
             
            $sensorType = Type::where('company_id', $user->company_id)
                ->where('name', 'sensor')
                ->first();
             
            $sensorModel = Model::where('company_id', $user->company_id)
                ->where('model_type_id', $sensorType->id)
                ->first();
            
            $partTire = Part::forceCreate([
                "company_id" => $user->company_id,
                "part_type_id" => $tireType->id,
                "part_model_id" => $tireModel->id,
                "number" => $json['id']."-tire",
                "position" => $this->validateNumeric($json['pos']),
                "vehicle_id" => $inputs['vehicle_id']
            ]);
            
            $part = Part::forceCreate([
                "company_id" => $user->company_id,
                "part_type_id" => $sensorType->id,
                "part_model_id" => $sensorModel->id,
                "number" => $json['id'],
                "position" => $this->validateNumeric($json['pos']),
                "vehicle_id" => $inputs['vehicle_id'],
                "part_id" => $this->validateNumeric($partTire->id)
            ]);
        }
        
        return $part;
    }
    
    private function validateNumeric($value)
    {
        return ( is_numeric($value) ? $value : null );
    }
}
