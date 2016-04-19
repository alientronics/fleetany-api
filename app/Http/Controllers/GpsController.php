<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Entities\Trip;
use App\Entities\User;
use Carbon\Carbon;
use App\Entities\Gps;

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
            $inputsCreate['latitude'] = $inputs['latitude'];
            $inputsCreate['longitude'] = $inputs['longitude'];
            $Gps = Gps::forceCreate($inputsCreate);
            
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
}
