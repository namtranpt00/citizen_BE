<?php

namespace App\Http\Controllers;

use App\Models\District;
use Illuminate\Http\Request;

class DistrictController extends Controller
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
        District::create([
            'id' => $id,
            "name" => $request->name
        ]);
        $district = District::findOrFail($id);
        $response = [
            'success' => true,
            'district' => $district
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
        } else
            return ['message' => 'cannot update district'];
    }

    public function list(Request $request)
    {
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
        return response($response, 201);
    }

    public function destroy($id)
    {
        District::where('id', $id)->delete();
        return [
            'message' => 'deleted district'
        ];
    }
}
