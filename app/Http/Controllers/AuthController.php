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
                'area' => 'required|string',
                'role' => 'required',
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
                    "area" => $fields['area'],
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
            $response = [
                'success' => false,
                'message' => "Cannot regist new account!!",
            ];
            return response($response, 200);
        }
    }

    public function update_user(Request $request, $id)
    {
        try {
            $fields = $request->validate([
                'password' => 'required|string',
                'permission' => 'required',
                'start_at' => 'required|date',
                'end_at' => 'nullable|date|after:start_at',
                'is_active' => 'nullable',
                'area' => 'required|string',
            ]);
            if ($fields['is_active'] == 0) {
                User::where('permission', 'like', $fields['permission'] . "%")->update(['is_active' => $fields['is_active']]);
            }
            $user = User::where('permission', $id)->where('is_deleted', 0);
//            return $user->get();
            if ($user) {
                $user->update([
                    "name" => $fields['permission'],
                    "password" => bcrypt($fields['password']),
                    "permission" => $fields['permission'],
                    "start_at" => $fields['start_at'],
                    "end_at" => $fields['end_at'],
                    "is_active" => $fields['is_active'],
                    "area" => $fields['area'],
                ]);
                $response = [
                    'success' => true,
                    'user' => $user->get()
                ];
                return response($response, 200);
            }
        } catch (\Exception $e) {
            return $e;
            $response = [
                'success' => false,
                'message' => "Cannot update user, please check the input again!!",
            ];
            return response($response, 200);
        }
    }


    public function delete_user($id)
    {
        try {
            User::where('permission', 'like', $id . "%")->update(['is_deleted' => 1]);
            return response([
                'success' => true,
                'message' => "deleted user"
            ], 200);
        } catch (\Exception $e) {
            return response([
                'success' => false,
                'message' => "cannot delete user !!"
            ], 200);
        }
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
                $is_active = $user['is_active'];
            } else $is_active = 0;

            if (!$user || !Hash::check($fields['password'], $user->password)) {
                return response([
                    'success' => false,
                    'message' => "user not found or password was wrong!!"
                ], 200);
            }

            $token = $user->createToken('myapptoken')->plainTextToken;
            $response = [
                'success' => true,
                'user' => $user,
                'is_acitve' => $is_active,
                'token' => $token
            ];
            return response($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => "User not found or password was wrong!!",
            ];
            return response($response, 200);
        }
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return [
            'message' => 'logged out'
        ];
    }

    public function list(Request $request){
        $fields = $request->validate([
            'name' => 'nullable|string',
            'permission' => 'nullable|string',
            'role' => 'required',
        ]);
        $users = User::where('role', $fields['role'] + 1)->where('is_deleted', 0)->orderBy('id');

        if (isset($request->permission) && $fields['role'] != 1) {
            $users = $users->where('permission', 'LIKE', $fields['permission'] . '%');
        }
        if (isset($request->name)) {
            $users = $users->where('name', 'LIKE', '%' . $fields['name'] . '%');
        }
        $response = [
            'success' => true,
            'users' => $users->get()
        ];
        return response($response, 200);
    }
}
