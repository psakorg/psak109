<?php

namespace App\Http\Controllers\report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function checkEntity($entity_number)
    {
        try {
            $entity = DB::table('tbl_pt')
                ->where('id_pt', $entity_number)
                ->first();

            if ($entity) {
                return response()->json([
                    'success' => true,
                    'entity_name' => $entity->nama_pt
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Entity tidak ditemukan'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in checkEntity: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari data entity'
            ], 500);
        }
    }

    public function checkAccount($account_number, Request $request)
    {
        try {
            $entity_number = $request->query('entity_number');
            $user = Auth::user();
            
            // Jika bukan superadmin, cek apakah entity number sesuai dengan id_pt user
            if ($user->role !== 'superadmin' && $user->id_pt != $entity_number) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk melihat data entity ini'
                ], 403);
            }

            $account = DB::table('tblmaster_tmp')
                ->where('no_acc', $account_number)
                ->where('id_pt', $entity_number)
                ->first();

            if ($account) {
                return response()->json([
                    'success' => true,
                    'deb_name' => $account->deb_name
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Account tidak ditemukan'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in checkAccount: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari data account'
            ], 500);
        }
    }

    public function checkAccountCorporate($account_number, Request $request)
    {
        try {
            $entity_number = $request->query('entity_number');
            $user = Auth::user();
            
            // Jika bukan superadmin, cek apakah entity number sesuai dengan id_pt user
            if ($user->role !== 'superadmin' && $user->id_pt != $entity_number) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk melihat data entity ini'
                ], 403);
            }

            $account = DB::table('tblmaster_tmpcorporate')
                ->where('no_acc', $account_number)
                ->where('no_branch', $entity_number)
                ->first();

            if ($account) {
                return response()->json([
                    'success' => true,
                    'deb_name' => $account->deb_name
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Account tidak ditemukan'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in checkAccount: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari data account'
            ], 500);
        }
    }
}

