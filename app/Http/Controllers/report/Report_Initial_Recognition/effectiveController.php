<?php

namespace App\Http\Controllers\report\Report_Initial_Recognition;

use App\Http\Controllers\Controller;
use App\Models\InitialRecognitionEffective;
use Illuminate\Http\Request;
use Carbon\Carbon;

class effectiveController extends Controller
{
    public function index(Request $request)
    {
        $branch = $request->input('branch');
        $tahun = $request->input('tahun') ?? date('Y');
        $bulan = $request->input('bulan') ?? date('m');

        $loans = InitialRecognitionEffective::getInitialRecognition($branch, $tahun, $bulan);

        // $result1 = InitialRecognitionEffective::getInitialRecognition('999', '2024', '5');
        // dd($result1);
        
        return view('report.initial_recognition.effective.master', compact('loans', 'bulan', 'tahun'));
    }
}
