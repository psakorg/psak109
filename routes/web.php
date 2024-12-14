<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MappingController;
use App\Http\Controllers\ManajemenController;
use App\Http\Controllers\ManajemenControllerAdmin;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\dashboard\SecuritiesDashboardController;
use App\Http\Controllers\dashboard\simple_interestDashboardController;
// upload
use App\Http\Controllers\upload\simple_interest\tblcorporateController;
use App\Http\Controllers\upload\simple_interest\tblmasterController as tblmaster_SI;
use App\Http\Controllers\upload\effective\tblmasterController as tblmaster_EFF;
use App\Http\Controllers\Auth\AuthenticatedSessionController;


use App\Http\Controllers\MappingAdminController;

// report
use App\Http\Controllers\report\Report_Accrual_Interest\simpleinterestController as acrualsiControler;
use App\Http\Controllers\report\Report_Accrual_Interest\effectiveController as acrualeffControler;

use App\Http\Controllers\report\Report_Amortised_Cost\simpleinterestController as amorcostsiController;
use App\Http\Controllers\report\Report_Amortised_Cost\effectiveController as amorcosteffControler;

use App\Http\Controllers\report\Report_Amortised_Initial_Cost\simpleinterestController as amorinitcostsiControler;
use App\Http\Controllers\report\Report_Amortised_Initial_Cost\effectiveController as amorinitcosteffControler;

use App\Http\Controllers\report\Report_Amortised_Initial_Fee\simpleinterestController as amorinitfeesiControler;
use App\Http\Controllers\report\Report_Amortised_Initial_Fee\effectiveController as amorinitfeeeffControler;

use App\Http\Controllers\report\Report_Expective_Cash_Flow\simpleinterestController as expectcfsiControler;
use App\Http\Controllers\report\Report_Expective_Cash_Flow\effectiveController as expectcfeffControler;

use App\Http\Controllers\report\Report_Interest_Deffered\simpleinterestController as interestdeffsiControler;
use App\Http\Controllers\report\Report_Interest_Deffered\effectiveController as interestdeffeffControler;

use App\Http\Controllers\report\Report_Journal\simpleinterestController as journalsiControler;
use App\Http\Controllers\report\Report_Journal\effectiveController as journaleffControler;

use App\Http\Controllers\report\Report_Outstanding\simpleinterestController as outstandsiControler;
use App\Http\Controllers\report\Report_Outstanding\effectiveController as outstandeffControler;

use App\Http\Controllers\report\securities\calculatedaccrualcouponController;
use App\Http\Controllers\report\securities\amortisedcostController;
use App\Http\Controllers\report\securities\amortisedinitialdiscController;
use App\Http\Controllers\report\securities\amortisedinitialpremController;
use App\Http\Controllers\report\securities\expectedcashflowController;

use App\Http\Controllers\report\Report_Initial_Recognition\effectiveController as initialRecognitionEffectiveController;

use App\Models\Mapping;

// Rute untuk halaman utama
Route::get('/kontak', function(){
    return view('landing.kontak');
});
Route::get('/artikel', function(){
    return view('landing.artikel.artikelload');
});
Route::get('/artikel/perbedaanpsak', function(){
    return view('landing.artikel.perbedaanpsak');
});
Route::get('/artikel/ojk', function(){
    return view('landing.artikel.ojk');
});
Route::get('/tentang', function(){
    return view('landing.tentang');
});
Route::get('/', function () {
    return view('landing/home');
});

// Rute untuk profil pengguna
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/photo', [ProfileController::class, 'uploadPhoto'])->name('profile.photo.upload');
});

// auth laravel breeze
require __DIR__.'/auth.php';

// Rute untuk dashboard
Route::middleware(['auth', 'verified'])->group(function () {
    // Normal User Dashboard
    Route::get('/user/dashboard', [DashboardUserController::class, 'index'])
    ->middleware('user')
    ->name('dashboard');
    Route::get('/dashboard/debitur/{no_acc}', [DashboardUserController::class, 'getDebiturInfo']);

    // Admin Dashboard
    Route::get('/admin/dashboard', [DashboardAdminController::class, 'index'])
    ->middleware('admin')
    ->name('admin.dashboard');
    Route::get('/dashboard/debitur/{no_acc}', [DashboardAdminController::class, 'getDebiturInfo']);

    // Super Admin Dashboard
    Route::get('/superadmin/dashboard', function () {
        return view('superadmin/dashboard');
    })->middleware('superadmin')->name('superadmin.dashboard');
    //dashboard simpleinterest

    Route::get('/admin/dashboard/simple_interest', [simple_interestDashboardController::class, 'index'])
    ->middleware('admin')
    ->name('admin.dashboard');
    Route::get('/dashboard/simple_interest/debitur/{no_acc}', [simple_interestDashboardController::class, 'getDebiturInfo']);
    //dashboard securities

    Route::get('/admin/dashboard/securities', [SecuritiesDashboardController::class, 'index'])
    ->middleware('admin')
    ->name('admin.dashboard');
    Route::get('/dashboard/securities/debitur/{no_acc}', [SecuritiesDashboardController::class, 'getDebiturInfo']);

});

// Rute untuk halaman pricing user
Route::get('/pricing', [MappingController::class, 'show'])->name('pricing.show');
Route::post('/mapping/save', [MappingController::class, 'save'])->name('mapping.save');


// Rute untuk manajemen user (Super Admin)
Route::middleware(['auth', 'superadmin'])->group(function () {
    Route::get('/usermanajemen', [ManajemenController::class, 'index'])->name('usermanajemen');
    Route::get('/add/user', [ManajemenController::class, 'tambahuser'])->name('superadmin.add.user');
    Route::post('/add/user', [ManajemenController::class, 'AddUser'])->name('superadmin.AddUser');

    // Rute untuk edit user superadmin
    Route::get('/edit/user/{user_id}', [ManajemenController::class, 'loadedit'])->name('superadmin.edit.user');
    Route::post('/edit/user/{user_id}', [ManajemenController::class, 'EditUser'])->name('superadmin.update.user');

    // Rute untuk delete user superadmin
    Route::get('/delete/user/{user_id}', [ManajemenController::class, 'delete'])->name('superadmin.delete.user');
});

// Rute untuk manajemen user (Admin)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/usermanajemen', [ManajemenControllerAdmin::class, 'index'])->name('admin.usermanajemen');
    Route::get('/admin/add/user', [ManajemenControllerAdmin::class, 'loadadduseradmin'])->name('load.admin.add.user');
    Route::post('/admin/add/user', [ManajemenControllerAdmin::class, 'AddUserAdmin'])->name('AddUserAdmin');
    Route::get('/admin/edit/user/{user_id}', [ManajemenControllerAdmin::class, 'loadeditadmin'])->name('admin.edit.user');
    Route::post('/admin/edit/user/{user_id}', [ManajemenControllerAdmin::class, 'EditUserAdmin'])->name('admin.update.user');
    Route::get('/admin/delete/user/{user_id}', [ManajemenControllerAdmin::class, 'deleteadmin'])->name('admin.delete.user');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/mappings', [MappingAdminController::class, 'index'])->name('mappings.index');
    Route::get('/mappings/{userId}', [MappingAdminController::class, 'show'])->name('mappings.show');
});

// Rute untuk report accrual simple interest

Route::middleware(['auth'])->group(function () {
    Route::get('/report-accrual-simple-interest', [acrualsiControler::class, 'index'])->name('report-acc-si.index');
    Route::get('/report/accrual-interest/simple-interest/view/{no_acc}/{id_pt}', [acrualsiControler::class, 'view'])->name('report-acc-si.view');
    Route::get('/report-accrual-simple-interest/export-pdf/{no_acc}/{id_pt}', [acrualsiControler::class, 'exportPdf'])->name('report-acc-si.exportPdf');
    Route::get('/report-accrual-simple-interest/export-excel/{no_acc}/{id_pt}', [acrualsiControler::class, 'exportExcel'])->name('report-acc-si.exportExcel');
});
// Rute untuk report accrual effective
Route::middleware(['auth'])->group(function () {
    Route::get('/report-accrual-effective', [acrualeffControler::class, 'index'])->name('report-acc-eff.index');
    Route::get('/report-accrual-effective/view/{no_acc}/{id_pt}', [acrualeffControler::class, 'view'])->name('report-acc-eff.view');
    Route::get('/report-accrual-effective/export-pdf/{no_acc}/{id_pt}', [acrualeffControler::class, 'exportPdf'])->name('report-acc-eff.exportPdf');
    Route::get('/report-accrual-effective/export-excel/{no_acc}/{id_pt}', [acrualeffControler::class, 'exportExcel'])->name('report-acc-eff.exportExcel');
});

// Rute untuk report amortised cost simple interest
Route::middleware(['auth'])->group(function () {
    Route::get('/report-amortised-cost-simple-interest', [amorcostsiController::class, 'index'])->name('report-amorcost-si.index');
    Route::get('/report-amortised-cost-simple-interest/view/{no_acc}/{id_pt}', [amorcostsiController::class, 'view'])->name('report-amorcost-si.view');
    Route::get('/report-amortised-cost-simple-interest/export-pdf/{no_acc}/{id_pt}', [amorcostsiController::class, 'exportPdf'])->name('report-amorcost-si.exportPdf');
    Route::get('/report-amortised-cost-simple-interest/export-excel/{no_acc}/{id_pt}', [amorcostsiController::class, 'exportExcel'])->name('report-amorcost-si.exportExcel');
});
// Rute untuk report amortised cost effective
Route::middleware(['auth'])->group(function () {
    Route::get('/report-amortised-cost-effective', [amorcosteffControler::class, 'index'])->name('report-amorcost-eff.index');
    Route::get('/report-amortised-cost-effective/view/{no_acc}/{id_pt}', [amorcosteffControler::class, 'view'])->name('report-amorcost-eff.view');
    Route::get('/report-amortised-cost-effective/export-pdf/{no_acc}/{id_pt}', [amorcosteffControler::class, 'exportPdf'])->name('report-amorcost-eff.exportPdf');
    Route::get('/report-amortised-cost-effective/export-excel/{no_acc}/{id_pt}', [amorcosteffControler::class, 'exportExcel'])->name('report-amorcost-eff.exportExcel');
});

// Rute untuk report amortised initial cost simple interest
Route::middleware(['auth'])->group(function () {
    Route::get('/report-amortised-initial-cost-simple-interest', [amorinitcostsiControler::class, 'index'])->name('report-amorinitcost-si.index');
    Route::get('/report-amortised-initial-cost-simple-interest/view/{no_acc}/{id_pt}', [amorinitcostsiControler::class, 'view'])->name('report-amorinitcost-si.view');
    Route::get('/report-amortised-initial-cost-simple-interest/export-pdf/{no_acc}/{id_pt}', [amorinitcostsiControler::class, 'exportPdf'])->name('report-amorinitcost-si.exportPdf');
    Route::get('/report-amortised-initial-cost-simple-interest/export-excel/{no_acc}/{id_pt}', [amorinitcostsiControler::class, 'exportExcel'])->name('report-amorinitcost-si.exportExcel');
});
// Rute untuk report amortised-initial-cost effective
Route::middleware(['auth'])->group(function () {
    Route::get('/report-amortised-initial-cost-effective', [amorinitcosteffControler::class, 'index'])->name('report-amorinitcost-eff.index');
    Route::get('/report-amortised-initial-cost-effective/view/{no_acc}/{id_pt}', [amorinitcosteffControler::class, 'view'])->name('report-amorinitcost-eff.view');
    Route::get('/report-amortised-initial-cost-effective/export-pdf/{no_acc}/{id_pt}', [amorinitcosteffControler::class, 'exportPdf'])->name('report-amorinitcost-eff.exportPdf');
    Route::get('/report-amortised-initial-cost-effective/export-excel/{no_acc}/{id_pt}', [amorinitcosteffControler::class, 'exportExcel'])->name('report-amorinitcost-eff.exportExcel');
});

// Rute untuk report amortised initial fee simple interest
Route::middleware(['auth'])->group(function () {
    Route::get('/report-amortised-initial-fee-simple-interest', [amorinitfeesiControler::class, 'index'])->name('report-amorinitfee-si.index');
    Route::get('/report-amortised-initial-fee-simple-interest/view/{no_acc}/{id_pt}', [amorinitfeesiControler::class, 'view'])->name('report-amorinitfee-si.view');
    Route::get('/report-amortised-initial-fee-simple-interest/export-pdf/{no_acc}/{id_pt}', [amorinitfeesiControler::class, 'exportPdf'])->name('report-amorinitfee-si.exportPdf');
    Route::get('/report-amortised-initial-fee-simple-interest/export-excel/{no_acc}/{id_pt}', [amorinitfeesiControler::class, 'exportExcel'])->name('report-amorinitfee-si.exportExcel');
});
// Rute untuk report amortised-initial-cost effective
Route::middleware(['auth'])->group(function () {
    Route::get('/report-amortised-initial-fee-effective', [amorinitfeeeffControler::class, 'index'])->name('report-amorinitfee-eff.index');
    Route::get('/report-amortised-initial-fee-effective/view/{no_acc}/{id_pt}', [amorinitfeeeffControler::class, 'view'])->name('report-amorinitfee-eff.view');
    Route::get('/report-amortised-initial-fee-effective/export-pdf/{no_acc}/{id_pt}', [amorinitfeeeffControler::class, 'exportPdf'])->name('report-amorinitfee-eff.exportPdf');
    Route::get('/report-amortised-initial-fee-effective/export-excel/{no_acc}/{id_pt}', [amorinitfeeeffControler::class, 'exportExcel'])->name('report-amorinitfee-eff.exportExcel');
});

// Rute untuk report expective cash flow simple interest
Route::middleware(['auth'])->group(function () {
    Route::get('/report-expective-cash-flow-simple-interest', [expectcfsiControler::class, 'index'])->name('report-expectcf-si.index');
    Route::get('/report-expective-cash-flow-simple-interest/view/{no_acc}/{id_pt}', [expectcfsiControler::class, 'view'])->name('report-expectcf-si.view');
    Route::get('/report-expective-cash-flow-simple-interest/export-pdf/{no_acc}/{id_pt}', [expectcfsiControler::class, 'exportPdf'])->name('report-expectcf-si.exportPdf');
    Route::get('/report-expective-cash-flow-simple-interest/export-excel/{no_acc}/{id_pt}', [expectcfsiControler::class, 'exportExcel'])->name('report-expectcf-si.exportExcel');
});
// Rute untuk report expective cash flow effective
Route::middleware(['auth'])->group(function () {
    Route::get('/report-expective-cash-flow-effective', [expectcfeffControler::class, 'index'])->name('report-expectcfeff-eff.index');
    Route::get('/report-expective-cash-flow-effective/view/{no_acc}/{id_pt}', [expectcfeffControler::class, 'view'])->name('report-expectcfeff-eff.view');
    Route::get('/report-expective-cash-flow-effective/export-pdf/{no_acc}/{id_pt}', [expectcfeffControler::class, 'exportPdf'])->name('report-expectcfeff-eff.exportPdf');
    Route::get('/report-expective-cash-flow-effective/export-excel/{no_acc}/{id_pt}', [expectcfeffControler::class, 'exportExcel'])->name('report-expectcfeff-eff.exportExcel');
});

// Rute untuk report interest deferred simple interest
Route::middleware(['auth'])->group(function () {
    Route::get('/report-interest-deferred-simple-interest', [interestdeffsiControler::class, 'index'])->name('report-interestdeff-si.index');
    Route::get('/report-interest-deferred-simple-interest/view/{no_acc}/{id_pt}', [interestdeffsiControler::class, 'view'])->name('report-interestdeff-si.view');
    Route::get('/report-interest-deferred-simple-interest/export-pdf/{no_acc}/{id_pt}', [interestdeffsiControler::class, 'exportPdf'])->name('report-interestdeff-si.exportPdf');
    Route::get('/report-interest-deferred-simple-interest/export-excel/{no_acc}/{id_pt}', [interestdeffsiControler::class, 'exportExcel'])->name('report-interestdeff-si.exportExcel');
});
// Rute untuk report interest deferred effective
Route::middleware(['auth'])->group(function () {
    Route::get('/report-interest-deferred-effective', [interestdeffeffControler::class, 'index'])->name('report-interestdeff-eff.index');
    Route::get('/report-interest-deferred-effective/view/{no_acc}/{id_pt}', [interestdeffeffControler::class, 'view'])->name('report-interestdeff-eff.view');
    Route::get('/report-interest-deferred-effective/export-pdf/{no_acc}/{id_pt}', [interestdeffeffControler::class, 'exportPdf'])->name('report-interestdeff-eff.exportPdf');
    Route::get('/report-interest-deferred-effective/export-excel/{no_acc}/{id_pt}', [interestdeffeffControler::class, 'exportExcel'])->name('report-interestdeff-eff.exportExcel');
});

// Rute untuk report journal simple interest
Route::middleware(['auth'])->group(function () {
    Route::get('/report-journal-simple-interest', [journalsiControler::class, 'index'])->name('report-journal-si.index');
    Route::get('/report-journal-simple-interest/view/{no_acc}/{id_pt}', [journalsiControler::class, 'view'])->name('report-journal-si.view');
    Route::get('/report-journal-simple-interest/export-pdf/{no_acc}/{id_pt}', [journalsiControler::class, 'exportPdf'])->name('report-journal-si.exportPdf');
    Route::get('/report-journal-simple-interest/export-excel/{no_acc}/{id_pt}', [journalsiControler::class, 'exportExcel'])->name('report-journal-si.exportExcel');
});
// Rute untuk report journal effective
Route::middleware(['auth'])->group(function () {
    Route::get('/report-journal-effective', [journaleffControler::class, 'index'])->name('report-journal-eff.index');
    Route::get('/report-journal-effective/view/{no_acc}/{id_pt}', [journaleffControler::class, 'view'])->name('report-journal-eff.view');
    Route::get('/report-journal-effective/export-pdf/{no_acc}/{id_pt}', [journaleffControler::class, 'exportPdf'])->name('report-journal-eff.exportPdf');
    Route::get('/report-journal-effective/export-excel/{no_acc}/{id_pt}', [journaleffControler::class, 'exportExcel'])->name('report-journal-eff.exportExcel');
});

// Rute untuk report outstanding simple interest
Route::middleware(['auth'])->group(function () {
    Route::get('/report-outstanding-simple-interest', [outstandsiControler::class, 'index'])->name('report-outstanding-si.index');
    Route::get('/report-outstanding-simple-interest/view/{no_acc}/{id_pt}', [outstandsiControler::class, 'view'])->name('report-outstanding-si.view');
    Route::get('/report-outstanding-simple-interest/export-pdf/{no_acc}/{id_pt}', [outstandsiControler::class, 'exportPdf'])->name('report-outstanding-si.exportPdf');
    Route::get('/report-outstanding-simple-interest/export-excel/{no_acc}/{id_pt}', [outstandsiControler::class, 'exportExcel'])->name('report-outstanding-si.exportExcel');
});
// Rute untuk report outstanding effective
Route::middleware(['auth'])->group(function () {
    Route::get('/report-outstanding-effective', [outstandeffControler::class, 'index'])->name('report-outstanding-eff.index');
    Route::get('/report-outstanding-effective/view/{id_pt}', [outstandeffControler::class, 'view'])->name('report-outstanding-eff.view');
    Route::get('/report-outstanding-effective/export-pdf/{id_pt}', [outstandeffControler::class, 'exportPdf'])->name('report-outstanding-eff.exportPdf');
    Route::get('/report-outstanding-effective/export-excel/{id_pt}', [outstandeffControler::class, 'exportExcel'])->name('report-outstanding-eff.exportExcel');
});

                                                        // SECURITIES

// Rute untuk report calculated accrual coupon
Route::middleware(['auth'])->group(function () {
    Route::get('/report-calculated-accrual-coupon', [calculatedaccrualcouponController::class, 'index'])->name('report-calculated-accrual-coupon.index');
    Route::get('/report-calculated-accrual-coupon/view/{no_acc}/{id_pt}', [calculatedaccrualcouponController::class, 'view'])->name('report-calculated-accrual-coupon.view');
    Route::get('/report-calculated-accrual-coupon/export-pdf/{no_acc}/{id_pt}', [calculatedaccrualcouponController::class, 'exportPdf'])->name('report-calculated-accrual-coupon.exportPdf');
    Route::get('/report-calculated-accrual-coupon/export-excel/{no_acc}/{id_pt}', [calculatedaccrualcouponController::class, 'exportExcel'])->name('report-calculated-accrual-coupon.exportExcel');
});
// Rute untuk report amortised cost
Route::middleware(['auth'])->group(function () {
    Route::get('/report-amortised-cost', [amortisedcostController::class, 'index'])->name('report-amortised-cost.index');
    Route::get('/report-amortised-cost/view/{no_acc}/{id_pt}', [amortisedcostController::class, 'view'])->name('report-amortised-cost.view');
    Route::get('/report-amortised-cost/export-pdf/{no_acc}/{id_pt}', [amortisedcostController::class, 'exportPdf'])->name('report-amortised-cost.exportPdf');
    Route::get('/report-amortised-cost/export-excel/{no_acc}/{id_pt}', [amortisedcostController::class, 'exportExcel'])->name('report-amortised-cost.exportExcel');
});
// Rute untuk report amortised initial disc
Route::middleware(['auth'])->group(function () {
    Route::get('/report-amortised-initial-disc', [amortisedinitialdiscController::class, 'index'])->name('report-amortised-initial-disc.index');
    Route::get('/report-amortised-initial-disc/view/{no_acc}/{id_pt}', [amortisedinitialdiscController::class, 'view'])->name('report-amortised-initial-disc.view');
    Route::get('/report-amortised-initial-disc/export-pdf/{no_acc}/{id_pt}', [amortisedinitialdiscController::class, 'exportPdf'])->name('report-amortised-initial-disc.exportPdf');
    Route::get('/report-amortised-initial-disc/export-excel/{no_acc}/{id_pt}', [amortisedinitialdiscController::class, 'exportExcel'])->name('report-amortised-initial-disc.exportExcel');
});
// Rute untuk report amortised initial prem
Route::middleware(['auth'])->group(function () {
    Route::get('/report-amortised-initial-prem', [amortisedinitialpremController::class, 'index'])->name('report-amortised-initial-prem.index');
    Route::get('/report-amortised-initial-prem/view/{no_acc}/{id_pt}', [amortisedinitialpremController::class, 'view'])->name('report-amortised-initial-prem.view');
    Route::get('/report-amortised-initial-prem/export-pdf/{no_acc}/{id_pt}', [amortisedinitialpremController::class, 'exportPdf'])->name('report-amortised-initial-prem.exportPdf');
    Route::get('/report-amortised-initial-prem/export-excel/{no_acc}/{id_pt}', [amortisedinitialpremController::class, 'exportExcel'])->name('report-amortised-initial-prem.exportExcel');
});
// Rute untuk report expected cashflow
Route::middleware(['auth'])->group(function () {
    Route::get('/report-expected-cashflow', [expectedcashflowController::class, 'index'])->name('report-expected-cashflow.index');
    Route::get('/report-expected-cashflow/view/{no_acc}/{id_pt}', [expectedcashflowController::class, 'view'])->name('report-expected-cashflow.view');
    Route::get('/report-expected-cashflow/export-pdf/{no_acc}/{id_pt}', [expectedcashflowController::class, 'exportPdf'])->name('report-expected-cashflow.exportPdf');
    Route::get('/report-expected-cashflow/export-excel/{no_acc}/{id_pt}', [expectedcashflowController::class, 'exportExcel'])->name('report-expected-cashflow.exportExcel');
});



Route::get('/sedang-dalam-pengembangan', function () {
    return view('sedang-dalam-pengembangan');
})->name('under');

//uplaod loan detail simple interest
Route::middleware(['auth'])->group(function () {
    Route::get('/upload/tblcorporate', [tblcorporateController::class, 'index'])->name('corporate.index');
    Route::post('/execute-stored-procedure', [tblcorporateController::class, 'executeStoredProcedure'])->name('execute.stored.procedure');
    Route::post('/import-excel', [tblcorporateController::class, 'importExcel'])->name('import.excel');
    Route::post('/clear-corporate', [tblcorporateController::class, 'clear'])->name('corporate.clear');
});



Route::middleware(['auth'])->group(function () {
    // Group untuk Simple Interest
    Route::prefix('simple-interest')->group(function () {
        Route::get('/tblmaster', [tblmaster_SI::class, 'index'])
            ->name('simple-interest.tblmaster.index');
        Route::post('/tblmaster/import', [tblmaster_SI::class, 'importExcel'])
            ->name('simple-interest.tblmaster.import');
        Route::post('/tblmaster/execute-procedure', [tblmaster_SI::class, 'executeStoredProcedure'])
            ->name('simple-interest.tblmaster.execute-procedure');
        Route::post('/tblmaster/clear', [tblmaster_SI::class, 'clear'])
            ->name('simple-interest.tblmaster.clear');
    });
});
Route::middleware(['auth'])->group(function () {
    // Group untuk Effective
    Route::prefix('effective')->group(function () {
        Route::get('/tblmaster', [tblmaster_EFF::class, 'index'])
            ->name('effective.tblmaster.index');
        Route::post('/tblmaster/import', [tblmaster_EFF::class, 'importExcel'])
            ->name('effective.tblmaster.import');
        Route::post('/tblmaster/execute-procedure', [tblmaster_EFF::class, 'executeStoredProcedure'])
            ->name('effective.tblmaster.execute-procedure');
        Route::post('/tblmaster/clear', [tblmaster_EFF::class, 'clear'])
            ->name('effective.tblmaster.clear');
    });
});

Route::prefix('report-initial-recognition')->group(function () {
    Route::get('/', [initialRecognitionEffectiveController::class, 'index'])->name('report-initial-recognition.index');
    // Route::get('/effective', [InitialRecognitionController::class, 'effective'])->name('report-initial-recognition.effective');
    // Route::get('/simple-interest', [InitialRecognitionController::class, 'simpleInterest'])->name('report-initial-recognition.simple-interest');
});

