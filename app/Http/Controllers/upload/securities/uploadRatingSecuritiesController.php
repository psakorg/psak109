<?php

namespace App\Http\Controllers\upload\securities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Carbon\Carbon;

class uploadRatingSecuritiesController extends Controller
{
    public function index(Request $request)
    {
        $id_pt = Auth::user()->id;
        $perPage = $request->input('per_page', 10);

        $securities = DB::table('securities.tblratingcoasecurities')
        ->where('id', $id_pt)
        ->paginate($perPage);
        
        return view('upload.securities.layouts.tblratingsecurities', [
        'title' => 'Laravel - PHPSpreadsheet',
        'securities' => $securities 
    ]);
    }
}
