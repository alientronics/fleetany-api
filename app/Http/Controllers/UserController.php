<?php

namespace App\Http\Controllers;

use App\Entities\User;
use App\Http\Controllers\Controller;
use App\Entities\Vehicle;

class UserController extends Controller
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
  
        $Users = User::all();
  
        return response()->json($Users);
  
    }

    public function findOrCreateUser($email)
    {
        $authUser = User::where('email', $email)->first();
        if ($authUser) {
            $vehicles = Vehicle::where('company_id', $authUser->company_id)->get();
        } else {
            $newUser = new User([
                'name' => explode("@", $email)[0],
                'email' => $email,
                'contact_id' => 1,
            ]);
            $newUser->save();
    
            $newUser->setUp();
    
            $vehicles = Vehicle::where('company_id', $newUser->company_id)->get();
        }
        
        return response()->json($vehicles);
    }
}
