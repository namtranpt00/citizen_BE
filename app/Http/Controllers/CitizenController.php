<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Citizen;

class CitizenController extends Controller
{
    public function genID($request, $length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $request->permission . $randomString;
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'permission' => 'required|string',
                'name' => 'required|string',
                'ID_number' => 'nullable|string|unique:citizen,ID_number',
                'date_of_birth' => 'required|date',
                'gender' => 'required|integer',
                'hometown' => 'required|string',
                'permanent_address' => 'required|string',
                'temporary_address' => 'required|string',
                'religion' => 'required|string',
                'education_level' => 'required|string',
                'job' => 'required|string',
            ]);
            $id = $this->genID($request);
            Citizen::create([
                'id' => $id,
                "name" => $request->name,
                "ID_number" => $request->ID_number,
                "date_of_birth" => $request->date_of_birth,
                "gender" => $request->gender,
                "hometown" => $request->hometown,
                "permanent_address" => $request->permanent_address,
                "temporary_address" => $request->temporary_address,
                "religion" => $request->religion,
                "education_level" => $request->education_level,
                "job" => $request->job,
            ]);
            $citizen = Citizen::findOrFail($id);
            $response = [
                'success' => true,
                'citizen' => $citizen
            ];
            return response($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => "Cannot create citizen !!"
            ];
            return response($response, 200);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'permission' => 'required|string',
                'name' => 'required|string',
                'ID_number' => 'nullable|string',
                'date_of_birth' => 'required|date',
                'gender' => 'required|integer',
                'hometown' => 'required|string',
                'permanent_address' => 'required|string',
                'temporary_address' => 'required|string',
                'religion' => 'required|string',
                'education_level' => 'required|string',
                'job' => 'required|string',
            ]);
            $citizen = Citizen::findOrFail($id);
            if ($citizen) {
                $citizen->update([
                    "name" => $request->name,
                    "ID_number" => $request->ID_number,
                    "date_of_birth" => $request->date_of_birth,
                    "gender" => $request->gender,
                    "hometown" => $request->hometown,
                    "permanent_address" => $request->permanent_address,
                    "temporary_address" => $request->temporary_address,
                    "religion" => $request->religion,
                    "education_level" => $request->education_level,
                    "job" => $request->job,
                ]);
                $response = [
                    'success' => true,
                    'citizen' => $citizen
                ];
                return response($response, 200);
            } else {
                $response = [
                    'success' => false,
                    'message' => "Cannot update citizen !!"
                ];
                return response($response, 200);
            }
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => "Cannot update citizen !!"
            ];
            return response($response, 200);
        }
    }

    public function list(Request $request)
    {
        try {
            $request->validate([
                'permission' => 'required|string',
                'name' => 'nullable|string',
                'ID_number' => 'nullable|string',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|integer',
                'hometown' => 'nullable|string',
                'permanent_address' => 'nullable|string',
                'temporary_address' => 'nullable|string',
                'religion' => 'nullable|string',
                'education_level' => 'nullable|string',
                'job' => 'nullable|string',
            ]);

            $citizen = Citizen::where('id', 'like', $request->permission . '%')->orderBy('id');
            if (isset($request->name)) {
                $citizen = $citizen->where('name', 'LIKE', '%' . $request->name . '%');
            }
            if (isset($request->ID_number)) {
                $citizen = $citizen->where('ID_number', $request->ID_number);
            }
            if (isset($request->date_of_birth)) {
                $citizen = $citizen->where('date_of_birth', $request->date_of_birth);
            }
            if (isset($request->gender)) {
                $citizen = $citizen->where('gender', $request->gender);
            }
            if (isset($request->hometown)) {
                $citizen = $citizen->where('hometown', 'LIKE', '%' . $request->hometown . '%');
            }
            if (isset($request->permanent_address)) {
                $citizen = $citizen->where('permanent_address', 'LIKE', '%' . $request->permanent_address . '%');
            }
            if (isset($request->temporary_address)) {
                $citizen = $citizen->where('temporary_address', 'LIKE', '%' . $request->temporary_address . '%');
            }
            if (isset($request->religion)) {
                $citizen = $citizen->where('religion', 'LIKE', '%' . $request->religion . '%');
            }
            if (isset($request->education_level)) {
                $citizen = $citizen->where('education_level', 'LIKE', '%' . $request->education_level . '%');
            }
            if (isset($request->job)) {
                $citizen = $citizen->where('job', 'LIKE', '%' . $request->job . '%');
            }
            $citizen = $citizen->paginate(50);
            $response = [
                'success' => true,
                'citizen' => $citizen
            ];
            return response($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => "Cannot get citizen !!"
            ];
            return response($response, 200);
        }
    }

    public function destroy($id)
    {
        Citizen::where('id', $id)->delete();
        return [
            'message' => 'deleted citizen'
        ];
    }
}
