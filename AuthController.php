<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function signup(Request $request){

        $validator = Validator::make($request->all() , [

            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',

        ]);

        if($validator->fails())
        {
            return response()->json([

                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()->all()

            ] , 404);
        }

        $user = User::create([

            'name' => $request->name,
            'email' => $request->email,
           'password' => $request->password

        ]);

        return response()->json([

            'status' => true,
            'message' => 'User Created Successfully',
            'data' => $user

        ] , 200);
    }

    public function login(Request $request){

        $validator = Validator::make($request->all(), [

            'email' => 'required|email',
            'password' => 'required'

        ]);

        if($validator->fails()){
            return response()->json([

                'status' => false,
                'message' => 'Authentication Fails',
                'errors' => $validator->errors()->all(),

            ] , 404);
        }
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password ]))
        {

            $authUser = Auth::user();

            return response()->json([

                'status' => true,
                'message' => 'You Logged In Successfully',
                'token' => $authUser->createToken("API Token")->plainTextToken,
                'token_type' => 'bearer'

            ], 200);

        }
        else{
            
            return response()->json([

                'status' => false,
                'message' => 'Credential Does Not Match' 

            ], 404);
        }



    }
    public function logout(Request $request){   
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json([

            'status' => true,
            'message' => 'You Have Successfully Logged Out',
            'user' => $user

        ] , 200);
    }
}
