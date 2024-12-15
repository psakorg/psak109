<?php

namespace App\Http\Controllers\report\Report_Initial_Recognition;

use App\Http\Controllers\Controller;
use App\Models\InitialRecognitionEffective;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class effectiveController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = $user->role === 'superadmin';
        
        $branch = $request->input('branch', "999");
        $tahun = $request->input('tahun') ?? date('Y');
        $bulan = $request->input('bulan') ?? date('m');

        // $result1 = InitialRecognitionEffective::getInitialRecognition('999', '2024', '5');
        // dd($id_pt);

        $loans = InitialRecognitionEffective::getInitialRecognition($branch, $tahun, $bulan);
        
        return view('report.initial_recognition.effective.master', compact('loans', 'bulan', 'tahun', 'user', 'isSuperAdmin'));
    }
}
