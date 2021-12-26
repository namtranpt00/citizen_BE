<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\District;
use App\Models\Hamlet;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatisticController extends Controller
{
    public function mark_done(Request $request)
    {
        try {
            $fields = $request->validate([
                'permission' => 'required|string',
                'is_done' => 'required',
            ]);
            Hamlet::findOrFail($fields['permission'])->update(['is_done' => $fields['is_done']]);

            $ward_id = substr($fields['permission'], 0, 6);
            $num_of_hamlet = Hamlet::where('id', "LIKE", $ward_id. '%')->count();
            $num_of_hamlet_done = Hamlet::where('id', "LIKE", $ward_id. '%')->where('is_done', 1)->count();
            $hamlet_percentage = $num_of_hamlet_done * 100 / $num_of_hamlet;
            Ward::findOrFail($ward_id)->update(['is_done' => $hamlet_percentage]);

            $district_id = substr($fields['permission'], 0, 4);
            $num_of_ward = Ward::where('id', "LIKE", $district_id. '%')->count();
            $num_of_ward_done = Ward::where('id', "LIKE", $district_id. '%')->where('is_done', 100)->count();
            $ward_percentage = $num_of_ward_done * 100 / $num_of_ward;
            District::findOrFail($district_id)->update(['is_done' => $ward_percentage]);

            $province_id = substr($fields['permission'], 0, 2);
            $num_of_district = District::where('id', "LIKE", $province_id. '%')->count();
            $num_of_district_done = District::where('id', "LIKE", $province_id. '%')->where('is_done', 1)->count();
            $district_percentage = $num_of_district_done * 100 / $num_of_district;
            Province::findOrFail($province_id)->update(['is_done' => $district_percentage]);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function statistic(Request $request)
    {
        try {
            $fields = $request->validate([
                'permission' => 'nullable|string',
            ]);
            if( isset($fields['permission'])){
                $under15 = Citizen::where("id", "like", $fields['permission'] . "%")->whereRaw("TIMESTAMPDIFF(YEAR,citizen.date_of_birth,CURDATE()) <= ?", array(14))->count();
                $above60 = Citizen::where("id", "like", $fields['permission'] . "%")->whereRaw("TIMESTAMPDIFF(YEAR,citizen.date_of_birth,CURDATE()) >= ?", array(65))->count();
                $another = Citizen::where("id", "like", $fields['permission'] . "%")->count() - $under15 - $above60;
            } else {
                $under15 = Citizen::whereRaw("TIMESTAMPDIFF(YEAR,citizen.date_of_birth,CURDATE()) <= ?", array(14))->count();
                $above60 = Citizen::whereRaw("TIMESTAMPDIFF(YEAR,citizen.date_of_birth,CURDATE()) >= ?", array(65))->count();
                $another = Citizen::count() - $under15 - $above60;

            }
            $response = [
                'success' => true,
                'data' => [
                    'under15' => $under15,
                    '15to64' => $another,
                    'above64' => $above60
                ]
            ];
            return response($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => 'some thing went wrong'
            ];
            return response($response, 200);
        }
    }

    public function gender_statistic (Request $request){
        try {
            $fields = $request->validate([
                'permission' => 'nullable|string',
            ]);
            if( isset($fields['permission'])){
                $male = Citizen::where("id", "like", $fields['permission'] . "%")->where('gender' , 1)->count();
                $female = Citizen::where("id", "like", $fields['permission'] . "%")->where('gender' , 2)->count();
                $total = Citizen::where("id", "like", $fields['permission'] . "%")->count();
            } else {
                $male = Citizen::where('gender' , 1)->count();
                $female = Citizen::where('gender' , 2)->count();
                $total = Citizen::count();
            }

            $response = [
                'success' => true,
                'data' => [
                    'male' => $male,
                    'female' => $female,
                    'total' => $total
                ]
            ];
            return response($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => 'some thing went wrong'
            ];
            return response($response, 200);
        }
    }

    public function amount_statistic(Request $request){
        try {
            $fields = $request->validate([
                'permission' => 'nullable|string',
            ]);
            $data = [];
            if (strlen($fields['permission']) == 0){
                $provinces = Province::get();
                foreach ($provinces as $key => $province){
                    $amount = Citizen::where('id' , 'like',  $province['id']. "%")->count();
                    $obj = (object) array("name" => $province['name'], 'amount' => $amount);
                    array_push($data, $obj);
                }
            }
            if (strlen($fields['permission']) == 2){
                $districts = District::where('id' , "like", $fields['permission']. "%")->get();
                foreach ($districts as $key => $district){
                    $amount = Citizen::where('id' , 'like',  $district['id']. "%")->count();
                    $obj = (object) array("name" => $district['name'], 'amount' => $amount);
                    array_push($data, $obj);
                }

            }
            if (strlen($fields['permission']) == 4){
                $wards = Ward::where('id' , "like", $fields['permission']. "%")->get();
                foreach ($wards as $key => $ward){
                    $amount = Citizen::where('id' , 'like',  $ward['id']. "%")->count();
                    $obj = (object) array("name" => $ward['name'], 'amount' => $amount);
                    array_push($data, $obj);
                }
            }
            if (strlen($fields['permission']) == 6){
                $hamlets = Hamlet::where('id' , "like", $fields['permission']. "%")->get();
                foreach ($hamlets as $key => $hamlet){
                    $amount = Citizen::where('id' , 'like',  $hamlet['id']. "%")->count();
                    $obj = (object) array("name" => $hamlet['name'], 'amount' => $amount);
                    array_push($data, $obj);
                }
            }
            $response = [
                'success' => true,
                'data' => $data
            ];
            return $response;
        } catch (\Exception $e){
            return $e;
        }
    }
}
