<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $fields = $request->validate([
                'password' => 'required|string',
                'permission' => 'required|string',
                'role' => 'required',
                'start_at' => 'required|date',
                'end_at' => 'required|date|after:start_at'
            ]);
            $isExist = User::where('permission', $fields['permission'])
                ->where('is_deleted', 0)
                ->first();
            if (!$isExist) {
                $user =  User::create([
                    "name" => $fields['permission'],
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
            } else {
                $response = [
                    'success' => false,
                    'message' => "Account already exists !!",
                ];
                return response($response, 201);
            }
        } catch (\Exception $e) {
            return $e;
            $response = [
                'success' => false,
                'message' => "Cannot regist new account!!",
            ];
            return response($response, 201);
        }

    }


    public function update_user(Request $request, $id)
    {
        try {
            $fields = $request->validate([
                'password' => 'required|string',
                'permission' => 'required',
                'role' => 'required',
                'start_at' => 'required|date',
                'end_at' => 'required|date|after:start_at'
            ]);
            $user = User::findOrFail($id);
            if ($user) {
                $user->update([
                    "name" => $fields['permission'],
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
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => "Cannot update user, please check the input again!!",
            ];
            return response($response, 201);
        }
    }

    public function delete_user($id)
    {
        User::where('permission', 'like', $id . "%")->update(['is_deleted' => 1]);
        return response([
            'success' => true,
            'message' => "deleted user"
        ], 201);
    }

    public function login(Request $request)
    {
        try {
            $fields = $request->validate([
                'name' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = User::where('is_deleted', 0)
                ->where('name', $fields['name'])
                ->first();
            $now = date('Y-m-d');

            if ($now >= $user->start_at && $now <= $user->end_at) {
                $is_active = 1;
            } else $is_active = 0;

            if (!$user || !Hash::check($fields['password'], $user->password)) {
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

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return [
            'message' => 'logged out'
        ];
    }
}
