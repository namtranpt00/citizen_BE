<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
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
            $response = [
                'success' => true,
                'provinces' => $provinces->get()
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
        return [
            'message' => 'deleted province'
        ];
    }
}
