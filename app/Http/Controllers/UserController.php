<?php

namespace App\Http\Controllers;

use App\Entities\User;
use App\Http\Controllers\Controller;
use App\Entities\Vehicle;
use Illuminate\Http\Request;
use App\Entities\Type;

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
           
            $lang = !empty($request->input('lang')) ? $request->input('lang') : null;
            $user->setUp($lang);
        }

        $response['vehicles'] = $user->company->vehicles;
        
        $response['fuelTypes'] = Type::where('entity_key', 'fuel')
                    ->where('company_id', $user->company_id)
                    ->get();
        
        return response()->json($response);
    }
}
