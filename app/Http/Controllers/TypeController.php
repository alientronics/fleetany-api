<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Entities\Type;
use App\Entities\User;

class TypeController extends Controller
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

    public function getFuelType(Request $request)
    {
        $inputs = $request->all();
        $user = User::where('email', $inputs['email'])->first();
        $Types = Type::where('entity_key', 'fuel')
                    ->where('company_id', $user->company_id)
                    ->get();
  
        return response()->json($Types);
  
    }
}
