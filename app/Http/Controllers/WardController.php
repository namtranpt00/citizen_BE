<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ward;

class WardController extends Controller
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

            Ward::create([
                'id' => $id,
                "name" => $request->name
            ]);
            $ward = Ward::findOrFail($id);
            $response = [
                'success' => true,
                'ward' => $ward
            ];
            return response($response, 200);
        } catch (\Exception $e){
            return response([
                'success' => false,
                'message' => "cannot create ward !!"
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
            $ward = Ward::findOrFail($id);
            $newID = $this->genID($request);
            if ($ward) {
                $ward->update([
                    'id' => $newID,
                    'name' => $request->name,
                ]);
                $response = [
                    'success' => true,
                    'ward' => $ward
                ];
                return response($response, 200);
            } else {
                return response([
                    'success' => false,
                    'message' => "cannot update ward !!"
                ], 200);
            }
        } catch (\Exception $e){
            return response([
                'success' => false,
                'message' => "cannot update ward !!"
            ], 200);
        }

    }

    public function list(Request $request)
    {
        try {
            $request->validate([
                'id' => 'nullable|string',
                'name' => 'nullable|string',
                'permission' => 'required|string',
            ]);

            $wards = Ward::where('id', 'like', $request->permission . '%')->orderBy('id');
            if (isset($request->id)) {
                $wards = $wards->where('id', $this->genID($request));
            }
            if (isset($request->name)) {
                $wards = $wards->where('name', 'LIKE', '%' . $request->name . '%');
            }
            $response = [
                'success' => true,
                'wards' => $wards->get()
            ];
            return response($response, 200);
        } catch (\Exception $e){
            return response([
                'success' => false,
                'message' => "cannot get wards !!"
            ], 200);
        }
    }

    public function destroy($id)
    {
        Ward::where('id', $id)->delete();
        return [
            'message' => 'deleted ward'
        ];
    }
}
