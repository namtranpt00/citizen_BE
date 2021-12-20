<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\District;
use App\Models\Hamlet;
use App\Models\Province;
use App\Models\Ward;

class RegulateController extends Controller
{
    public function mark_done (Request $request) {
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
        } catch (\Exception $e){
            return $e;
        }
    }
}
