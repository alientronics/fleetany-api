<?php

namespace App\Http\Controllers;

use App\Entities\User;
use App\Http\Controllers\Controller;
use App\Entities\Vehicle;
use Illuminate\Http\Request;

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

    public function findOrCreateUser(Request $request)
    {
        $email = $request->input('email');
        $user = User::where('email', $email)->first();
        if (!$user) {
            $user = new User([
                'name' => explode("@", $email)[0],
                'email' => $email,
                'contact_id' => 1,
            ]);
            $user->save();
            $user->setUp();
        }

        $vehicles = $user->company->vehicles;
        return response()->json($vehicles);
    }
}
