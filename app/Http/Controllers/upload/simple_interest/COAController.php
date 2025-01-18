<?php

namespace App\Http\Controllers\upload\simple_interest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log;

class COAController extends Controller
{
    public function index(Request $request)
    {

        $user = Auth::user();
        $id_pt = $user->id_pt;
        if (!$id_pt) {
            abort(404, 'Invalid ID');
        }
    
        // Validate if the authenticated user has access to this `id_pt`'
        if ($id_pt != Auth::user()->id_pt) {
            abort(403, 'Unauthorized');
        }

        $interface = $request->query('interface');
        $coa = $request->query('coa');
        $group = $request->query('group');
        $perPage = $request->query('per_page', 20);

        $loans = DB::table('public.tblcoaloancorporate')
        ->where('no_branch', $id_pt)
        ->when($interface, function($query, $interface) {
            return $query->sortBy('interface', $interface);
        })
        ->when($coa, function($query, $coa) {
            return $query->sortBy('coa', $coa);
        })
        ->when($group, function($query, $group) {
            return $query->sortBy('GROUP', $group);
        })
        ->orderBy('id', 'asc')
        ->paginate($perPage);

        // $loans = UploadSimpleInterest::getCoaSimple($id_pt);
       //dd($loans);
        //dd($interface, $coa, $group);
        //dd($request->all());
        $isSuperAdmin = $user->role === 'superadmin';

        return view('upload.simple_interest.layouts.coa', compact('loans', 'interface','coa','group','isSuperAdmin'));
    }
}
