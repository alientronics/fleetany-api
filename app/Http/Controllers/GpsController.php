<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Entities\User;
use App\Entities\Gps;
use Log;

class GpsController extends Controller
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
  
        $Gps = Gps::all();
  
        return response()->json($Gps);
  
    }
  
    public function create(Request $request)
    {
        $gpsData = $request->all();
        
        if (!empty($gpsData)) {
            Log::info('GPS Data: '.json_encode($gpsData));
            
            $jsonData = json_decode($gpsData['json'], true);

            foreach ($jsonData as $json) {
                $inputs = $this->validateNumericNullables($json);

                $user = User::where('email', $gpsData['email'])->first();
                $inputsCreate['company_id'] = $user->company_id;
                $inputsCreate['vehicle_id'] = $gpsData['vehicle_id'];
                $inputsCreate['driver_id'] = $user->contact->id;
                $inputsCreate['latitude'] = $inputs['latitude'];
                $inputsCreate['longitude'] = $inputs['longitude'];
                $inputsCreate['accuracy'] = $inputs['accuracy'];
                $inputsCreate['altitude'] = $inputs['altitude'];
                $inputsCreate['altitudeAccuracy'] = $inputs['altitudeAccuracy'];
                $inputsCreate['heading'] = $inputs['heading'];
                $inputsCreate['speed'] = $inputs['speed'];
                $Gps = Gps::forceCreate($inputsCreate);
                    
                if (is_numeric($Gps->id)) {
                    $success = true;
                } else {
                    $success = false;
                }
            }
        }
        
        return response()->json(["success" => $success]);
    }

    private function validateNumericNullables($gpsData)
    {
        $fields = ['accuracy', 'altitude', 'altitudeAccuracy', 'heading', 'speed'];
        
        foreach ($fields as $field) {
            $gpsData[$field] = ( !empty($gpsData[$field]) && is_numeric($gpsData[$field]) ?
                                                    $gpsData[$field] : null );
        }
        
        return $gpsData;
    }
}
