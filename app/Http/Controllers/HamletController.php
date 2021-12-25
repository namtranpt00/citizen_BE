<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Hamlet;

class HamletController extends Controller
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
            Hamlet::create([
                'id' => $id,
                "name" => $request->name,
            ]);
            $hamlet = Hamlet::findOrFail($id);
            $response = [
                'success' => true,
                'hamlet' => $hamlet
            ];
            return response($response, 200);
        } catch (\Exception $e) {
            return response([
                'success' => false,
                'message' => "cannot create hamlet !!"
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
            $hamlet = Hamlet::findOrFail($id);
            $newID = $this->genID($request);

            if ($hamlet) {
                $hamlet->update([
                    'id' => $newID,
                    'name' => $request->name,
                ]);
                $response = [
                    'success' => true,
                    'hamlet' => $hamlet
                ];
                return response($response, 200);
            } else {
                return response([
                    'success' => false,
                    'message' => "Cannot update hamlet !!"
                ], 200);
            }
        } catch (\Exception $e){
            return response([
                'success' => false,
                'message' => "Cannot update hamlet !!"
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

            $hamlets = Hamlet::where('id', 'like', $request->permission . '%')->orderBy('id');
            if (isset($request->id)) {
                $hamlets = $hamlets->where('id', $this->genID($request));
            }
            if (isset($request->name)) {
                $hamlets = $hamlets->where('name', 'LIKE', '%' . $request->name . '%');
            }

            $response = [
                'success' => true,
                'hamlets' => $hamlets->get()
            ];
            return response($response, 200);
        } catch (\Exception $e){
            return response([
                'success' => false,
                'message' => "Cannot get hamlets !!"
            ], 200);
        }
    }

    public function destroy($id)
    {
        Hamlet::where('id', $id)->delete();
        User::where('permission', 'like', $id . "%")->update(['is_deleted' => 1]);

        return [
            'message' => 'deleted hamlet'
        ];
    }
}
