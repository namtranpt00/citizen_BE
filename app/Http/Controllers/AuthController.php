<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(Request $request){
        $fields = $request -> validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
            'permission' => 'required',
            'role' => 'required',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at'
        ]);

        $user = User::create([
            "name" => $fields['name'],
            "email" => $fields['email'],
            "password" => bcrypt($fields['password']),
            "permission" => $fields['permission'],
            "role" => $fields['role'],
            "start_at" => $fields['start_at'],
            "end_at" => $fields['end_at'],
        ]);
        $response = [
            'success' => true,
            'user' => $user
        ];

        return response($response, 201);
    }

    public function update_user(Request $request, $id){
        $fields = $request-> validate([
            'name' => 'required|string',
            'email' => 'required|string',
            'password' => 'required|string',
            'permission' => 'required',
            'role' => 'required',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at'
        ]);
        $user = User::findOrFail($id);
        if($user){
            $user->update([
                "name" => $fields['name'],
                "email" => $fields['email'],
                "password" => bcrypt($fields['password']),
                "permission" => $fields['permission'],
                "role" => $fields['role'],
                "start_at" => $fields['start_at'],
                "end_at" => $fields['end_at'],
            ]);
            $response = [
                'success' => true,
                'user' => $user
            ];
            return response($response, 201);
        }
        else
            return ['message' => 'cannot update user'];
    }

    public function delete_user($id){
        $user = User::findOrFail($id);
        if($user)
            $user->update([
                'is_deleted' => 1
            ]);
        else
            return ['message' => 'user not found'];
        return [
            'message' => 'deleted user'
        ];
    }
    public function login(Request $request){
        try {
            $fields = $request -> validate([
                'email' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = User::where('is_deleted', 0)
                ->where('email', $fields['email'])
                ->first();
            $now = date('Y-m-d');

            if ($now >= $user->start_at && $now <= $user->end_at){
                $is_active = 1;
            } else $is_active = 0;

            if (!$user || !Hash::check($fields['password'], $user->password)){
                return response([
                    'success' => false,
                    'message' => "user not found or password was wrong!!"
                ], 201);
            }

            $token = $user->createToken('myapptoken')->plainTextToken;
            $response = [
                'success' => true,
                'user' => $user,
                'is_acitve' => $is_active,
                'token' => $token
            ];
            return response($response, 201);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => "User not found or password was wrong!!",
            ];
            return response($response, 201);
        }
    }

    public function logout(Request $request){
        auth()->user()->tokens()->delete();
        return [
            'message' => 'logged out'
        ];
    }
}
