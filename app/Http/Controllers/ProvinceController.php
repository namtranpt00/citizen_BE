<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public function store(Request $request){
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
        return response($response, 201);
    }

    public function update(Request $request, $id){
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
        else
            return ['message' => 'cannot update province'];
    }

    public function list(Request $request){
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
        return response($response, 201);
    }

    public function destroy($id){
        Province::where('id', $id)->delete();
        return [
            'message' => 'deleted province'
        ];
    }
}
