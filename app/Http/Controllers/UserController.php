<?php

namespace App\Http\Controllers;

use App\Entities\User;
use App\Http\Controllers\Controller;
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

    public function findOrCreateUser($email)
    {
        $authUser = User::where('email', $email)->first();
        if ($authUser) {
            return $authUser;
        }
        $newUser = new User([
            'name' => explode("@", $email)[0],
            'email' => $email,
            'contact_id' => 1,
        ]);
        $newUser->save();
        
        $newUser->setUp();
        return $newUser;
    }
}
