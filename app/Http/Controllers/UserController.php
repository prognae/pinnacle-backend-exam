<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //
    public function getUsers() {
        try {
            // return a message if there is no user found
            if(!User::all()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No user found.'
                ], 404); 
            }
            else {
                return response(User::all()); // return all users 
            }
        }
        catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }            
    }

    public function getSpecificUser($id) {
        try {
            // return a message if the ID does not match a user's ID
            if(!User::find($id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found.'
                ], 404);
            }
            else {
                return response(User::find($id), 200); // display user with that ID
            }
        }
        catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }            
    }

    public function updateUser(Request $request, $id) {
        try {
            // return a message if there is no user found
            $user = User::find($id);

            if(!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found.'
                ], 404);
            }
            else {
                // validate all request data first and return an error if validation fails
                $validatedRequest = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email'
                ]);

                if($validatedRequest->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Request validation error.',
                        'errors' => $validatedRequest->errors()
                    ], 401);
                };

                // update the user
                $user->update([
                    'name' => $request->input('name'),
                    'email' => $request->input('email')
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'User information updated successfully.'
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

    public function deleteUser(Request $request, $id) {
        try {
            // check first if the desired user is existing
            $user = User::find($id);        

            if(!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found.'
                ], 404);
            }
            else {
                // automatically logout the user if they delete the currently logged in account
                if($user == Auth::user()) {
                    $user->delete();

                    Auth::logout();
    
                    $request->session()->invalidate();
                
                    $request->session()->regenerateToken();
                    
                    return response()->json([
                        'status' => true,
                        'message' => 'Deleted user was the currently logged in user. You have been logged out. Please log in again.'
                    ], 200);
                }
                
                $user->delete(); // delete the user 

                return response()->json([
                    'status' => true,
                    'message' => 'User deleted successfully.'
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
}
