<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    //
    public function createUser(Request $request) {
        try {
            // check if the parameters in the request are all valid and return an error if not
            $validateUser = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8'
            ]);

            if($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Request validation error.',
                    'errors' => $validateUser->errors()
                ], 401);
            };

            $createUser = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')), // hash the password before storing in the database
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User created successfully.',
                'token' => $createUser->createToken("API TOKEN")->plainTextToken
            ], 200);
        }
        catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }    
    }

    public function checkAuth() {
        try {
            // return a message if the user is logged in or not 
            if(!Auth::check()) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not currently logged in.'
                ], 401);
            }
            else {
                return response()->json([
                    'status' => true,
                    'message' => 'You are already logged in.'
                ], 200);
            }
        }
        catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }            
    }

    public function login(Request $request) {
        try {
            // validate request parameters 
            $validateUser = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Request validation error.',
                    'errors' => $validateUser->errors()
                ], 401);
            };

            // log in logic with session
            if(Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
                $currentUser = User::where('email', $request->input('email'))->first();
                $request->session()->regenerate();

                return response()->json([
                    'status' => true,
                    'message' => 'Login Successful.',
                    'token' => $currentUser->createToken("API TOKEN")->plainTextToken
                ], 200);
            }
            else {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Credentials.'
                ], 401);
            }
            
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }        
    }

    public function logout(Request $request) {
        // logout - session destroy
        Auth::logout();
 
        $request->session()->invalidate();
    
        $request->session()->regenerateToken();

        return response()->json([
            'status' => true,
            'message' => 'Account logged out successfully.'
        ], 200);
    }
}
