<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Entities\User;
use App\Entities\TireSensor;
use App\Entities\Part;
use Log;
use App\Entities\Type;
use App\Entities\Model;
use GuzzleHttp\Client;
use App\Entities\Entry;
use App\Entities\PartEntry;

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

                    $this->checkAlerts($user, $tireSensor, $inputs['vehicle_id']);
                }
            }
        }
        
        Log::info('TireSensor Data: '.json_encode($inputs));
            
        return (new \Illuminate\Http\Response)->setStatusCode(200);
    }
    
    private function generateEntry($company_id, $tireSensor)
    {
        $tireSensor = TireSensor::where('part_id', $tireSensor->part_id)
            ->where('created_at', '<', $tireSensor->created_at)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!empty($tireSensor->id)) {
            $response = $this->getAlertType($company_id, $tireSensor->id);
            $objAlert = json_decode((string)$response->getBody());
            
            if (!empty($objAlert->id) && ($objAlert->id == 'HighPressure' || $objAlert->id == 'LowPressure')) {
                $entry_type = Type::select('id')->where('company_id', $company_id)
                    ->where(function ($query) {
                        $query->where('name', 'calibration maintenance')
                            ->orWhere('name', 'manuten&ccedil;&atilde;o de calibragem');
                    })
                    ->first();
                
                if (!empty($entry_type)) {
                    $entry = Entry::forceCreate([
                        "company_id" => $company_id,
                        "entry_type_id" => $entry_type->id,
                        "datetime_ini" => date("Y-m-d H:i:s"),
                        "cost" => 1,
                    ]);
                    
                    $part = Part::find($tireSensor->part_id);
                    
                    if(!empty($part->part_id)) {
                        PartEntry::forceCreate([
                            "part_id" => $part->part_id,
                            "entry_id" => $entry->id,
                        ]);
                    }
                }
            }
        }
    }
    
    private function checkAlerts($user, $tireSensor, $vehicle_id)
    {
        try {
            $client = new Client();
            $alerts = $client->request('POST', env('ALERTS_API_URL').'/api/v1/alert'.
                '?api_token=' . env('ALERTS_API_KEY'), [
                    'form_params' => ["company_id" => $user->company_id,
                        "tiresensor_id" => $tireSensor->id,
                        "vehicle_id" => $vehicle_id
                    ]
                ]);
            
            $objAlert = json_decode((string)$alerts->getBody());
            if (empty($objAlert->id) || ($objAlert->id != 'HighPressure' && $objAlert->id != 'LowPressure')) {
                $this->generateEntry($user->company_id, $tireSensor);
            }
        } catch (\Exception $e) {
            Log::info('TireSensor Alert Fail: '.$e->getMessage());
        }
    }
    
    private function getAlertType($company_id, $tiresensor_id)
    {
        try {
            $client = new Client();
            return $client->request('GET', env('ALERTS_API_URL').'/api/v1/get-alert-type/'.
                $company_id.'/'.$tiresensor_id.'?api_token=' . env('ALERTS_API_KEY'));
        } catch (\Exception $e) {
            Log::info('TireSensor Alert Type Fail: '.$e->getMessage());
            return false;
        }
    }
    
    private function getPart($user, $json, $inputs)
    {
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
