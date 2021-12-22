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
            switch (strlen($fields['permission'])) {
                case 8:
                    Hamlet::findOrFail($fields['permission'])->update(['is_done' => $fields['is_done']]);
                    break;
                case 6:
                    Ward::findOrFail($fields['permission'])->update(['is_done' => $fields['is_done']]);
                    break;
                case 4:
                    District::findOrFail($fields['permission'])->update(['is_done' => $fields['is_done']]);
                    break;
                case 2:
                    Province::findOrFail($fields['permission'])->update(['is_done' => $fields['is_done']]);
                    break;
            }
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function statistic(Request $request)
    {
        try {
            $fields = $request->validate([
                'permission' => 'required|string',
            ]);
            $under15 = Citizen::where("id", "like", $fields['permission'] . "%" )->whereRaw("TIMESTAMPDIFF(YEAR,citizen.date_of_birth,CURDATE()) <= ?" , array(14))->count();
            $above60 = Citizen::where("id", "like", $fields['permission'] . "%" )->whereRaw("TIMESTAMPDIFF(YEAR,citizen.date_of_birth,CURDATE()) >= ?" , array(65))->count();
            $another = Citizen::where("id", "like", $fields['permission'] . "%" )->count() - $under15 - $above60;
            $response = [
                'success' => true,
                'data' => [
                    'under15' => $under15,
                    '15to64' => $another,
                    'above64' => $above60
                ]
            ];
            return response($response, 200);
            return $under15;

        } catch (\Exception $e) {

        }
    }


}
