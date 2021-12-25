<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\User;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public function count_done($provinces){
        $count = 0;
        foreach ($provinces as $province){
            if ($province['is_done'] == 1) $count ++;
        }
        return $count;
    }
    public function store(Request $request){
        try {
            $request->validate([
                'id' => 'required|string|unique:provinces',
                'name' => 'required|string',
            ]);
            Province::create([
                'id' => $request->id,
                "name" => $request->name,
            ]);
            $province = Province::findOrFail($request->id);
            $response = [
                'success' => true,
                'province' => $province
            ];
            return response($response, 200);
        } catch (\Exception $e){
            return response([
                'success' => false,
                'message' => "cannot create province !!"
            ], 200);
        }
    }

    public function update(Request $request, $id){
        try {
            $request->validate([
                'id' => 'required|string',
                'name' => 'required|string',
            ]);
            $province = Province::findOrFail($id);
            if($province){
                $province->update([
                    'id' => $request->id,
                    'name' => $request->name,
                ]);
                $response = [
                    'success' => true,
                    'province' => $province
                ];
                return response($response, 201);
            }
            else {
                return response([
                    'success' => false,
                    'message' => "cannot update province !!"
                ], 200);
            }
        } catch (\Exception $e){
            return response([
                'success' => false,
                'message' => "cannot update province !!"
            ], 200);
        }
    }

    public function list(Request $request){
        try {
            $provinces = Province::orderBy('id');
            if (isset($request->id)) {
                $provinces = $provinces->where('id', $request->id);
            }
            if (isset($request->name)) {
                $provinces = $provinces->where('name', 'LIKE', '%' . $request->name . '%');
            }
            $provinces = $provinces->get();

            $response = [
                'success' => true,
                'provinces' => $provinces,
                'num_of_done' => $this->count_done($provinces)
            ];
            return response($response, 200);
        } catch (\Exception $e) {
            return response([
                'success' => false,
                'message' => "cannot get province !!"
            ], 200);
        }
    }

    public function destroy($id){
        Province::where('id', $id)->delete();
        User::where('permission', 'like', $id . "%")->update(['is_deleted' => 1]);
        return [
            'message' => 'deleted province'
        ];
    }
}
