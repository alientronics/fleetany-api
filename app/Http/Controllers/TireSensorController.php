<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Entities\User;
use App\Entities\TireSensor;
use App\Entities\Part;
use Log;

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

            if(!empty($inputs['dataIsCompress']) && $inputs['dataIsCompress'] == 1) {
                $inputs['json'] = $this->getZipContent($inputs['json']);
            }
            $jsonData = json_decode($inputs['json'], true);
           
            $user = User::where('email', $inputs['email'])->first();

            foreach ($jsonData as $json) {
                if (isset($json['id']) && isset($json['tp']) && isset($json['pr'])) {
                    $part = Part::select('id')->where('number', $json['id'])
                        ->where('company_id', $user->company_id)
                        ->first();
    
                    TireSensor::forceCreate(["latitude" => $this->validateNumeric($json['latitude']),
                        "longitude" => $this->validateNumeric($json['longitude']),
                        //"created_at" => ( $this->validateDate($json['ts']) ?
                        //    $json['ts'] : \DB::raw('NOW()') ),
                        "number" => $json['id'],
                        "temperature" => $this->validateNumeric($json['tp']),
                        "pressure" => $this->validateNumeric($json['pr']),
                        "battery" => $this->validateNumeric($json['ba']),
                        "part_id" => ( !empty($part->id) ? $part->id : null )
                    ]);
                }
            }
        }
        
        Log::info('TireSensor Data: '.json_encode($inputs));
            
        $success = true;
        
        return response()->json(["success" => $success]);
    }
    
    private function validateNumeric($value)
    {
        return ( is_numeric($value) ? $value : null );
    }
}
