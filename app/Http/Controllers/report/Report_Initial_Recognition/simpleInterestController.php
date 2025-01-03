<?php

namespace App\Http\Controllers\report\Report_Initial_Recognition;

use App\Http\Controllers\Controller;
use App\Models\InitialRecognitionSimpleInterest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class simpleInterestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('https://psak.pramatech.id');
        }

        $isSuperAdmin = $user->role === 'superadmin';
        
        $branch = $request->input('branch', $user->id_pt);

        if($branch != $user->id_pt){
            $branch = $user->id_pt;
        }

        $tahun = $request->input('tahun') ?? date('Y');
        $bulan = $request->input('bulan') ?? date('m');

        // $result1 = InitialRecognitionEffective::getInitialRecognition('999', '2024', '5');
        // dd($id_pt);

        $loans = InitialRecognitionSimpleInterest::getInitialRecognition($branch, $tahun, $bulan);

        //dd($loans);
        
        return view('report.initial_recognition.simple_interest.master', compact('loans', 'bulan', 'tahun', 'user', 'isSuperAdmin'));
    }
}
