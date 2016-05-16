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
        //try {
            $inputs = $request->all();
            
            $user = User::where('email', $inputs['email'])->first();
            $inputsCreate['company_id'] = $user->company_id;
            $inputsCreate['vehicle_id'] = $inputs['vehicle_id'];
            $inputsCreate['driver_id'] = $user->contact->id;
            $inputsCreate['latitude'] = $inputs['latitude'];
            $inputsCreate['longitude'] = $inputs['longitude'];
            $Gps = Gps::forceCreate($inputsCreate);

            //file_put_contents('gps.log','GPS Data: '.json_encode($inputs)."\n",FILE_APPEND);
            $this->insertTireSensors($inputs, $inputsCreate);
            
        if (is_numeric($Gps->id)) {
            $success = true;
        } else {
            $success = false;
        }
        //} catch (\Exception $e) {
        //    $success = false;
        //}
        
        return response()->json(["success" => $success]);
    }
    
    private function insertTireSensors($inputs, $inputsCreate)
    {
        if (!empty($inputs['json'])) {
            $json = $this->parseJson($inputs['json']);

            if ( isset($json['id']) && isset($json['tp']) && isset($json['pr']) ) {

                //foreach ($data as $json) {
                    $part = Part::select('id')->where('number', $json['id'])->first();
            
                    TireSensor::forceCreate(["latitude" => $this->validateNumeric($inputsCreate['latitude']),
                        "longitude" => $this->validateNumeric($inputsCreate['longitude']),
                        //"created_at" => ( $this->validateDate($json['ts']) ?
                        //    $json['ts'] : \DB::raw('NOW()') ),
                        "number" => $json['id'],
                        "temperature" => $this->validateNumeric($json['tp']),
                        "pressure" => $this->validateNumeric($json['pr']),
                        "part_id" => ( !empty($part->id) ? $part->id : null )
                    ]);
                //}
            }
        }
    }
    
    private function parseJson($json)
    {
        $json = stripslashes($json);
        $json = str_replace('rn', '', $json);
        $data = json_decode($json, true);
        if (json_last_error() > 0) {
            $pattern = '/id":(\d+),/i';
            $replacement = 'id":"${1}",';
            $json = preg_replace($pattern, $replacement, $json);
            $data = json_decode($json, true);
        }
        return $data;
    }


    private function validateNumeric($value)
    {
        return ( is_numeric($value) ? $value : null );
    }
    
    /*private function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $dateFormatted = \DateTime::createFromFormat($format, $date);
        return $dateFormatted && $dateFormatted->format($format) == $date;
    }*/
}
