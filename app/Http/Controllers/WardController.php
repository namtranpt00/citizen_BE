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
        return response($response, 201);
    }

    public function update(Request $request, $id)
    {
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
            return response($response, 201);
        } else
            return ['message' => 'cannot update ward'];
    }

    public function list(Request $request)
    {
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
        return response($response, 201);
    }

    public function destroy($id)
    {
        Ward::where('id', $id)->delete();
        return [
            'message' => 'deleted ward'
        ];
    }
}
