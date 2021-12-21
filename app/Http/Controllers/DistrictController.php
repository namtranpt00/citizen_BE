<?php

namespace App\Http\Controllers;

use App\Models\District;
use Illuminate\Http\Request;
use PHPUnit\Exception;

class DistrictController extends Controller
{
    public function genID($request){
        return $request->permission . $request->id;
    }
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|string',
                'name' => 'required|string',
                'permission' => 'required|string',
            ]);
            $id = $this->genID($request);
            District::create([
                'id' => $id,
                "name" => $request->name
            ]);
            $district = District::findOrFail($id);
            $response = [
                'success' => true,
                'district' => $district
            ];
            return response($response, 200);
        } catch (Exception $e){
            return response([
                'success' => false,
                'message' => "cannot create district !!"
            ], 200);
        }

    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'id' => 'required|string',
                'name' => 'required|string',
                'permission' => 'required|string',
            ]);
            $district = District::findOrFail($id);
            $newID = $this->genID($request);
            if ($district) {
                $district->update([
                    'id' => $newID,
                    'name' => $request->name,
                ]);
                $response = [
                    'success' => true,
                    'district' => $district
                ];
                return response($response, 201);
            } else {
                return response([
                    'success' => false,
                    'message' => "cannot update district !!"
                ], 200);
            }
        } catch (\Exception $e){
            return response([
                'success' => false,
                'message' => "cannot update district !!"
            ], 200);
        }

    }

    public function list(Request $request)
    {
        try {
            $districts = District::where('id', 'like', $request->permission . '%')->orderBy('id');
            if (isset($request->id)) {
                $districts = $districts->where('id', $this->genID($request));
            }
            if (isset($request->name)) {
                $districts = $districts->where('name', 'LIKE', '%' . $request->name . '%');
            }
            $response = [
                'success' => true,
                'districts' => $districts->get()
            ];
            return response($response, 200);
        } catch (\Exception $e){
            return response([
                'success' => false,
                'message' => "cannot get districts !!"
            ], 200);
        }
    }

    public function destroy($id)
    {
        District::where('id', $id)->delete();
        return [
            'message' => 'deleted district'
        ];
    }
}
