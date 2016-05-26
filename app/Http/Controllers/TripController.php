<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Entities\Trip;
use App\Entities\User;
use Carbon\Carbon;
use Log;
use App\Entities\Type;

class TripController extends Controller
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
  
        $Trips = Trip::all();
  
        return response()->json($Trips);
  
    }
  
    public function create(Request $request)
    {
        try {
            $inputs = $request->all();
            $user = User::where('email', $inputs['email'])->first();
            $inputsCreate['vehicle_id'] = $inputs['vehicle_id'];
            $inputsCreate['driver_id'] = $user->contact->id;
            $inputsCreate['fuel_cost'] = $inputs['fuel_cost'];
            $inputsCreate['fuel_amount'] = $inputs['fuel_amount'];
            $inputsCreate['end_mileage'] = $inputs['end_mileage'];
            $inputsCreate['fuel_type'] = $inputs['fuel_type'];
            $inputsCreate['tank_fill_up'] = $inputs['tank_fill_up'];
            $inputsCreate['company_id'] = $user->company_id;
            $type = Type::where('company_id', $inputsCreate['company_id'])
                        ->where('entity_key', 'trip')
                        ->first();
            $inputsCreate['trip_type_id'] = $type->id;
            $inputsCreate['pickup_date'] = Carbon::now();
            $Trip = Trip::forceCreate($inputsCreate);

            if (is_numeric($Trip->id)) {
                $statusCode = 200;
            } else {
                $statusCode = 400;
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            $statusCode = 400;
        }
        
        return (new \Illuminate\Http\Response)->setStatusCode($statusCode);
    }
}
