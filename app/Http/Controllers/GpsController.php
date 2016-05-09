<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Entities\User;
use App\Entities\Gps;
use App\Entities\TireSensor;
use App\Entities\Part;

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
        try {
            $inputs = $request->all();
            
            $user = User::where('email', $inputs['email'])->first();
            $inputsCreate['company_id'] = $user->company_id;
            $inputsCreate['vehicle_id'] = $inputs['vehicle_id'];
            $inputsCreate['driver_id'] = $user->contact->id;
            $inputsCreate['latitude'] = $inputs['latitude'];
            $inputsCreate['longitude'] = $inputs['longitude'];
            $Gps = Gps::forceCreate($inputsCreate);

            if (!empty($inputs['json'])) {
                $data = json_decode($inputs['json']);
                
                foreach ($data as $json) {
                    $part = Part::select('id')->where('number', $json->id)->first();
                    
                    TireSensor::forceCreate(["latitude" => ( is_numeric($inputsCreate['latitude']) ? $inputsCreate['latitude'] : null ),
                                            "longitude" => ( is_numeric($inputsCreate['longitude']) ? $inputsCreate['longitude'] : null ),
                                            "created_at" => ( $this->validateDate($json->ts) ? $json->ts : \DB::raw('NOW()') ),
                                            "number" => $json->id,
                                            "temperature" => ( is_numeric($json->temp) ? $json->temp : null ),
                                            "pressure" => ( is_numeric($json->press) ? $json->press : null ),
                                            "part_id" => ( $part->id ?: null )
                    ]);
                }
            }
            
            if (is_numeric($Gps->id)) {
                $success = true;
            } else {
                $success = false;
            }
        } catch (\Exception $e) {
            $success = false;
        }
        
        return response()->json(["success" => $success]);
    }
    
    private function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}
