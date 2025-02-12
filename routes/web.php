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
use App\Http\Controllers\upload\effective\OutstandingController;
use App\Http\Controllers\upload\simple_interest\OutstandingController as Outstanding_SI;
use App\Http\Controllers\upload\effective\COAControllerEffective;
use App\Http\Controllers\upload\simple_interest\COAControllerCorporateloan;
use App\Http\Controllers\upload\securities\uploadTblMasterTmpBidController;
use App\Http\Controllers\upload\securities\uploadDataSecuritiesController;
use App\Http\Controllers\upload\securities\uploadPriceSecuritiesController;
use App\Http\Controllers\upload\securities\uploadCoaSecuritiesController;
use App\Http\Controllers\upload\securities\uploadRatingSecuritiesController;
use App\Http\Controllers\report\securities\journalsecuritiesController;
use App\Http\Controllers\report\securities\journalsecuritiesControllerDaily;

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
use App\Http\Controllers\report\securities\initialRecognitionTreasuryController;
use App\Http\Controllers\report\securities\amortisedinitialbrokeragefeeController;
use App\Http\Controllers\report\securities\outstandingBalanceTreasuryController;
use App\Http\Controllers\report\securities\outstandingBalanceAmortizedCostController;
use App\Http\Controllers\report\securities\evaluationTreasuryController;

use App\Http\Controllers\report\Report_Initial_Recognition\effectiveController as initialRecognitionEffectiveController;
use App\Http\Controllers\report\Report_Initial_Recognition\simpleInterestController as initialRecognitionSimpleInterestController;

use App\Models\Mapping;

use App\Http\Controllers\dashboardController;

use App\Http\Controllers\report\ReportController;


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

    Route::get('/check-report-accrual-simple/{no_acc}/{id_pt}', [acrualsiControler::class, 'checkData'])->name('check-report-accrual-simple');
});
// Rute untuk report accrual effective
Route::middleware(['auth'])->group(function () {
    Route::get('/report-accrual-effective', [acrualeffControler::class, 'index'])->name('report-acc-eff.index');
    Route::get('/report-accrual-effective/view/{no_acc}/{id_pt}', [acrualeffControler::class, 'view'])->name('report-acc-eff.view');
    Route::get('/report-accrual-effective/export-pdf/{no_acc}/{id_pt}', [acrualeffControler::class, 'exportPdf'])->name('report-acc-eff.exportPdf');
    Route::get('/report-accrual-effective/export-excel/{no_acc}/{id_pt}', [acrualeffControler::class, 'exportExcel'])->name('report-acc-eff.exportExcel');
    Route::get('/check-report-accrual-effective/{no_acc}/{id_pt}', [acrualeffControler::class, 'checkData'])
        ->name('check-report-acc-eff');
});

// Rute untuk report amortised cost simple interest
Route::middleware(['auth'])->group(function () {
    Route::get('/report-amortised-cost-simple-interest', [amorcostsiController::class, 'index'])->name('report-amorcost-si.index');
    Route::get('/report-amortised-cost-simple-interest/view/{no_acc}/{id_pt}', [amorcostsiController::class, 'view'])->name('report-amorcost-si.view');
    Route::get('/report-amortised-cost-simple-interest/export-pdf/{no_acc}/{id_pt}', [amorcostsiController::class, 'exportPdf'])->name('report-amorcost-si.exportPdf');
    Route::get('/report-amortised-cost-simple-interest/export-excel/{no_acc}/{id_pt}', [amorcostsiController::class, 'exportExcel'])->name('report-amorcost-si.exportExcel');

    Route::get('/check-report-amortised-cost-simple/{no_acc}/{id_pt}', [amorcostsiController::class, 'checkData'])
        ->name('check-report-amortised-cost-simple');
});
// Rute untuk report amortised cost effectivev
Route::middleware(['auth'])->group(function () {
    Route::get('/report-amortised-cost-effective', [amorcosteffControler::class, 'index'])->name('report-amorcost-eff.index');
    Route::get('/report-amortised-cost-effective/view/{no_acc}/{id_pt}', [amorcosteffControler::class, 'view'])->name('report-amorcost-eff.view');
    Route::get('/report-amortised-cost-effective/export-pdf/{no_acc}/{id_pt}', [amorcosteffControler::class, 'exportPdf'])->name('report-amorcost-eff.exportPdf');
    Route::get('/report-amortised-cost-effective/export-excel/{no_acc}/{id_pt}', [amorcosteffControler::class, 'exportExcel'])->name('report-amorcost-eff.exportExcel');

    Route::get('/check-report-amortised-cost-effective/{no_acc}/{id_pt}', [amorcosteffControler::class, 'checkData'])
        ->name('check-report-amortised-cost-effective');
});

// Rute untuk report amortised initial cost simple interest
Route::middleware(['auth'])->group(function () {
    Route::get('/report-amortised-initial-cost-simple-interest', [amorinitcostsiControler::class, 'index'])->name('report-amorinitcost-si.index');
    Route::get('/report-amortised-initial-cost-simple-interest/view/{no_acc}/{id_pt}', [amorinitcostsiControler::class, 'view'])->name('report-amorinitcost-si.view');
    Route::get('/report-amortised-initial-cost-simple-interest/export-pdf/{no_acc}/{id_pt}', [amorinitcostsiControler::class, 'exportPdf'])->name('report-amorinitcost-si.exportPdf');
    Route::get('/report-amortised-initial-cost-simple-interest/export-excel/{no_acc}/{id_pt}', [amorinitcostsiControler::class, 'exportExcel'])->name('report-amorinitcost-si.exportExcel');

    Route::get('/check-report-amortised-initial-cost-simple/{no_acc}/{id_pt}',
        [amorinitcostsiControler::class, 'checkData'])
        ->name('check-report-amortised-initial-cost-simple');
});
// Rute untuk report amortised-initial-cost effective
Route::middleware(['auth'])->group(function () {
    Route::get('/report-amortised-initial-cost-effective', [amorinitcosteffControler::class, 'index'])->name('report-amorinitcost-eff.index');
    Route::get('/report-amortised-initial-cost-effective/view/{no_acc}/{id_pt}', [amorinitcosteffControler::class, 'view'])->name('report-amorinitcost-eff.view');
    Route::get('/report-amortised-initial-cost-effective/export-pdf/{no_acc}/{id_pt}', [amorinitcosteffControler::class, 'exportPdf'])->name('report-amorinitcost-eff.exportPdf');
    Route::get('/report-amortised-initial-cost-effective/export-excel/{no_acc}/{id_pt}', [amorinitcosteffControler::class, 'exportExcel'])->name('report-amorinitcost-eff.exportExcel');

    Route::get('/check-report-amortised-initial-cost-effective/{no_acc}/{id_pt}',
        [amorinitcosteffControler::class, 'checkData'])
        ->name('check-report-amortised-initial-cost-effective');
});

// Rute untuk report amortised initial fee simple interest
Route::middleware(['auth'])->group(function () {
    Route::get('/report-amortised-initial-fee-simple-interest', [amorinitfeesiControler::class, 'index'])->name('report-amorinitfee-si.index');
    Route::get('/report-amortised-initial-fee-simple-interest/view/{no_acc}/{id_pt}', [amorinitfeesiControler::class, 'view'])->name('report-amorinitfee-si.view');
    Route::get('/report-amortised-initial-fee-simple-interest/export-pdf/{no_acc}/{id_pt}', [amorinitfeesiControler::class, 'exportPdf'])->name('report-amorinitfee-si.exportPdf');
    Route::get('/report-amortised-initial-fee-simple-interest/export-excel/{no_acc}/{id_pt}', [amorinitfeesiControler::class, 'exportExcel'])->name('report-amorinitfee-si.exportExcel');

    Route::get('/check-report-amortised-initial-fee-simple/{no_acc}/{id_pt}', [amorinitfeesiControler::class, 'checkData'])
        ->name('check-report-amortised-initial-fee-simple');
});
// Rute untuk report amortised initial fee effective
Route::middleware(['auth'])->group(function () {
    Route::get('/report-amortised-initial-fee-effective', [amorinitfeeeffControler::class, 'index'])->name('report-amorinitfee-eff.index');
    Route::get('/report-amortised-initial-fee-effective/view/{no_acc}/{id_pt}', [amorinitfeeeffControler::class, 'view'])->name('report-amorinitfee-eff.view');
    Route::get('/report-amortised-initial-fee-effective/export-pdf/{no_acc}/{id_pt}', [amorinitfeeeffControler::class, 'exportPdf'])->name('report-amorinitfee-eff.exportPdf');
    Route::get('/report-amortised-initial-fee-effective/export-excel/{no_acc}/{id_pt}', [amorinitfeeeffControler::class, 'exportExcel'])->name('report-amorinitfee-eff.exportExcel');

    Route::get('/check-report-amortised-initial-fee-effective/{no_acc}/{id_pt}', [amorinitfeeeffControler::class, 'checkData'])
        ->name('check-report-amortised-initial-fee-effective');
});

// Rute untuk report expective cash flow simple interest
Route::middleware(['auth'])->group(function () {
    Route::get('/report-expective-cash-flow-simple-interest', [expectcfsiControler::class, 'index'])->name('report-expectcf-si.index');
    Route::get('/report-expective-cash-flow-simple-interest/view/{no_acc}/{id_pt}', [expectcfsiControler::class, 'view'])->name('report-expectcf-si.view');
    Route::get('/report-expective-cash-flow-simple-interest/export-pdf/{no_acc}/{id_pt}', [expectcfsiControler::class, 'exportPdf'])->name('report-expectcf-si.exportPdf');
    Route::get('/report-expective-cash-flow-simple-interest/export-excel/{no_acc}/{id_pt}', [expectcfsiControler::class, 'exportExcel'])->name('report-expectcf-si.exportExcel');

    Route::get('/check-report-expected-cashflow-simple/{no_acc}/{id_pt}',
        [expectcfsiControler::class, 'checkData'])
        ->name('check-report-expected-cashflow-simple');
});
// Rute untuk report expective cash flow effective
Route::middleware(['auth'])->group(function () {
    Route::get('/report-expective-cash-flow-effective', [expectcfeffControler::class, 'index'])->name('report-expectcfeff-eff.index');
    Route::get('/report-expective-cash-flow-effective/view/{no_acc}/{id_pt}', [expectcfeffControler::class, 'view'])->name('report-expectcfeff-eff.view');
    Route::get('/report-expective-cash-flow-effective/export-pdf/{no_acc}/{id_pt}', [expectcfeffControler::class, 'exportPdf'])->name('report-expectcfeff-eff.exportPdf');
    Route::get('/report-expective-cash-flow-effective/export-excel/{no_acc}/{id_pt}', [expectcfeffControler::class, 'exportExcel'])->name('report-expectcfeff-eff.exportExcel');

    Route::get('/check-report-expected-cashflow-effective/{no_acc}/{id_pt}',
        [expectcfeffControler::class, 'checkData'])
        ->name('check-report-expected-cashflow-effective');
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

// Rute untuk report journal simple interestt
Route::middleware(['auth'])->group(function () {
    Route::get('/report-journal-simple-interest', [journalsiControler::class, 'index'])->name('report-journal-si.index');
    Route::get('/report-journal-simple-interest/view/{no_acc}/{id_pt}', [journalsiControler::class, 'view'])->name('report-journal-si.view');
    Route::get('/report-journal-simple-interest/export-pdf/{id_pt}', [journalsiControler::class, 'exportPdf'])->name('report-journal-si.exportPdf');
    Route::get('/report-journal-simple-interest/export-excel/{id_pt}', [journalsiControler::class, 'exportExcel'])->name('report-journal-si.exportExcel');
    Route::get('/report-journal-simple-interest/export-report-excel', [journalsiControler::class, 'exportReportExcel'])->name('report-journal-si.exportReportExcel');
    Route::get('/report-journal-simple-interest/export-csv/{id_pt}', [journalsiControler::class, 'exportCsv'])->name('report-journal-si.exportCsv');
    Route::post('/report-journal-simple-interest/execute-procedure', [journalsiControler::class, 'executeStoredProcedure'])
        ->name('report-journal-si.execute-procedure');

    Route::get('/check-report-journal-simple/{no_acc}/{id_pt}',
        [journalsiControler::class, 'checkData'])
        ->name('check-report-journal-simple');
});
// Rute untuk report journal effective
Route::middleware(['auth'])->group(function () {
    Route::get('/report-journal-effective', [journaleffControler::class, 'index'])->name('report-journal-eff.index');
    Route::get('/report-journal-effective/view/{no_acc}/{id_pt}', [journaleffControler::class, 'view'])->name('report-journal-eff.view');
    Route::get('/report-journal-effective/export-pdf/{id_pt}', [journaleffControler::class, 'exportPdf'])->name('report-journal-eff.exportPdf');
    Route::get('/report-journal-effective/export-excel/{id_pt}', [journaleffControler::class, 'exportExcel'])->name('report-journal-eff.exportExcel');
    Route::get('/report-journal-effective/export-csv/{id_pt}', [journaleffControler::class, 'exportCsv'])->name('report-journal-eff.exportCsv');
    Route::get('/report-journal-effective/export-report-excel', [journaleffControler::class, 'exportReportExcel'])->name('report-journal-eff.exportReportExcel');
    Route::post('/report-journal-effective/execute-procedure', [journaleffControler::class, 'executeStoredProcedure'])
        ->name('report-journal-eff.execute-procedure');

    Route::get('/check-report-journal-effective/{no_acc}/{id_pt}',
        [journaleffControler::class, 'checkData'])
        ->name('check-report-journal-effective');
});

// Rute untuk report outstanding simple interest
Route::middleware(['auth'])->group(function () {
    Route::get('/report-outstanding-simple-interest', [outstandsiControler::class, 'index'])->name('report-outstanding-si.index');
    Route::get('/report-outstanding-simple-interest/view/{id_pt}', [outstandsiControler::class, 'view'])->name('report-outstanding-si.view');
    Route::get('/report-outstanding-simple-interest/export-pdf/{id_pt}', [outstandsiControler::class, 'exportPdf'])->name('report-outstanding-si.exportPdf');
    Route::get('/report-outstanding-simple-interest/export-excel/{id_pt}', [outstandsiControler::class, 'exportExcel'])->name('report-outstanding-si.exportExcel');
    Route::get('/report-outstanding-simple-interest/export-csv/{id_pt}', [outstandsiControler::class, 'exportCsv'])->name('report-outstanding-si.exportCsv');
    Route::get('report-acc-si/exportCsv/{no_acc}/{id_pt}', [outstandsiControler::class, 'exportCsv'])->name('report-acc-si.exportCsv');
    Route::get('/check-report-outstanding-simple/{no_acc}/{id_pt}',
        [outstandsiControler::class, 'checkData'])
        ->name('check-report-outstanding-simple');
});
// Rute untuk report outstanding effective
Route::middleware(['auth'])->group(function () {
    Route::get('/report-outstanding-effective', [outstandeffControler::class, 'index'])->name('report-outstanding-eff.index');
    Route::get('/report-outstanding-effective/view/{id_pt}', [outstandeffControler::class, 'view'])->name('report-outstanding-eff.view');
    Route::get('/report-outstanding-effective/export-pdf/{id_pt}', [outstandeffControler::class, 'exportPdf'])->name('report-outstanding-eff.exportPdf');

    Route::get('/report-outstanding-effective/export-excel/{id_pt}', [outstandeffControler::class, 'exportExcel'])->name('report-outstanding-eff.exportExcel');

    Route::get('/report-outstanding-effective/export-csv/{id_pt}', [outstandeffControler::class, 'exportCsv'])->name('report-outstanding-eff.exportCsv');

    Route::get('/check-report-outstanding-effective/{no_acc}/{id_pt}',
        [outstandeffControler::class, 'checkData'])
        ->name('check-report-outstanding-effective');
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
// Rute untuk report amortised Brokerage Fee
Route::middleware(['auth'])->group(function () {
    Route::get('/report-amortised-initial-brokerage-fee', [amortisedinitialbrokeragefeeController::class, 'index'])->name('report-amortised-initial-brokerage-fee.index');
    Route::get('/report-amortised-initial-brokerage-fee/view/{no_acc}/{id_pt}', [amortisedinitialbrokeragefeeController::class, 'view'])->name('report-amortised-initial-brokerage-fee.view');
    Route::get('/report-amortised-initial-brokerage-fee/export-pdf/{no_acc}/{id_pt}', [amortisedinitialbrokeragefeeController::class, 'exportPdf'])->name('report-amortised-initial-brokerage-fee.exportPdf');
    Route::get('/report-amortised-initial-brokerage-fee/export-excel/{no_acc}/{id_pt}', [amortisedinitialbrokeragefeeController::class, 'exportExcel'])->name('report-amortised-initial-brokerage-fee.exportExcel');
});
Route::middleware(['auth'])->group(function () {
    Route::prefix('securities')->group(function () {
        Route::get('/initial-recognition-treasury', [initialRecognitionTreasuryController::class, 'index'])
            ->name('securities.initial-recognition-treasury.index');

        Route::get('/outstanding-balance-treasury-bond', [outstandingBalanceTreasuryController::class, 'index'])
        ->name('securities.outstanding-balance-treasury.index');
        Route::post('/outstanding-balance-treasury-bond/execute-procedure', [outstandingBalanceTreasuryController::class, 'executeStoredProcedure'])
        ->name('securities.outstanding-balance-treasury.execute-procedure');
        Route::get('/outstanding-balance-treasury-bond/export-excel/{id_pt}', [outstandingBalanceTreasuryController::class, 'exportExcel'])->name('report-outstanding-securities.exportExcel');
        Route::get('/outstanding-balance-treasury-bond/export-pdf/{id_pt}', [outstandingBalanceTreasuryController::class, 'exportPdf'])->name('report-outstanding-securities.exportPDF');
        Route::get('/outstanding-balance-treasury-bond/export-csv/{id_pt}', [outstandingBalanceTreasuryController::class, 'exportCSV'])->name('report-outstanding-securities.exportcsv');


        Route::get('/outstanding-balance-amortized-cost', [outstandingBalanceAmortizedCostController::class, 'index'])
        ->name('securities.outstanding-balance-amortized-cost.index');
        Route::post('/outstanding-balance-amortized-cost/execute-procedure', [outstandingBalanceAmortizedCostController::class, 'executeStoredProcedure'])
        ->name('securities.outstanding-balance-amortized-cost.execute-procedure');
        Route::get('/outstanding-balance-amortized-cost/export-excel/{id_pt}', [outstandingBalanceAmortizedCostController::class, 'exportExcel'])->name('report-outstanding-amortized-cost.exportExcel');
        Route::get('/outstanding-balance-amortized-cost/export-pdf/{id_pt}', [outstandingBalanceAmortizedCostController::class, 'exportPdf'])->name('report-outstanding-amortized-cost.exportPDF');
        Route::get('/outstanding-balance-amortized-cost/export-csv/{id_pt}', [outstandingBalanceAmortizedCostController::class, 'exportCSV'])->name('report-outstanding-amortized-cost.exportcsv');


        Route::get('/evaluation-treasury-bond', [evaluationTreasuryController::class, 'index'])
        ->name('securities.evaluation-treasury-bond.index');
        Route::post('/evaluation-treasury-bond/execute-procedure', [evaluationTreasuryController::class, 'executeStoredProcedure'])
        ->name('securities.evaluation-treasury-bond.execute-procedure');
        Route::get('/evaluation-treasury-bond/export-excel/{id_pt}', [evaluationTreasuryController::class, 'exportExcel'])->name('report-evaluation-treasury-bond.exportExcel');
        Route::get('/evaluation-treasury-bond/export-pdf/{id_pt}', [evaluationTreasuryController::class, 'exportPdf'])->name('report-evaluation-treasury-bond.exportPdf');

        // Route upload untuk tblmaster_tmpbid
        Route::get('/upload/tblmaster', [uploadTblMasterTmpBidController::class, 'index'])
            ->name('upload.securities.tblmaster_tmpbid.index');
        Route::post('/upload/tblmaster/import', [uploadTblMasterTmpBidController::class, 'importExcel'])
            ->name('upload.securities.tblmaster_tmpbid.import');
        Route::post('/upload/tblmaster/execute-procedure', [uploadTblMasterTmpBidController::class, 'executeStoredProcedure'])
            ->name('upload.securities.tblmaster_tmpbid.execute-procedure');
        Route::post('/upload/tblmaster/clear', [uploadTblMasterTmpBidController::class, 'clear'])
            ->name('upload.securities.tblmaster_tmpbid.clear');

        // Route upload untuk data securities
        Route::get('/upload/data', [uploadDataSecuritiesController::class, 'index'])
            ->name('upload.securities.data.index');
        Route::post('/upload/data/import', [uploadDataSecuritiesController::class, 'importExcel'])
            ->name('upload.securities.data.import');
        Route::post('/upload/data/execute-procedure', [uploadDataSecuritiesController::class, 'executeStoredProcedure'])
            ->name('upload.securities.data.execute-procedure');
        Route::post('/upload/data/clear', [uploadDataSecuritiesController::class, 'clear'])
            ->name('upload.securities.data.clear');

        // Route upload untuk price securities
        Route::get('/upload/data/price', [uploadPriceSecuritiesController::class, 'index'])
            ->name('upload.price.securities.index');
        Route::post('/upload/price/import', [uploadPriceSecuritiesController::class, 'importExcel'])
            ->name('upload.securities.price.import');

        // Route upload untuk CoA securities
        Route::get('/upload/data/coa', [uploadCoaSecuritiesController::class, 'index'])
            ->name('upload.coa.securities.index');

        // Route upload untuk Rating securities
        Route::get('/upload/data/rating', [uploadRatingSecuritiesController::class, 'index'])
            ->name('upload.rating.securities.index');
        Route::post('/upload/rating/import', [uploadRatingSecuritiesController::class, 'importExcel'])
            ->name('upload.securities.rating.import');
        Route::post('/upload/rating/execute-procedure', [uploadRatingSecuritiesController::class, 'executeStoredProcedure'])
            ->name('upload.securities.rating.execute-procedure');



        Route::get('/securities/amortisedcostcontroller', [amortisedcostController::class, 'index'])->name('securities.index');
        Route::get('/securities/amortisedcostcontroller/view/{no_acc}/{id_pt}', [amortisedcostController::class, 'view'])->name('securities.view');
        Route::get('/securities/amortisedcostcontroller/export-pdf/{no_acc}/{id_pt}', [amortisedcostController::class, 'exportPdf'])->name('securities.exportPdf');
        Route::get('/securities/amortisedcostcontroller/export-excel/{no_acc}/{id_pt}', [amortisedcostController::class, 'exportExcel'])->name('securities.exportExcel');

        Route::get('/securities/amortisedinitialdiscController', [amortisedinitialdiscController::class, 'index'])->name('securities.index');
        Route::get('/securities/amortisedinitialdiscController/view/{no_acc}/{id_pt}', [amortisedinitialdiscController::class, 'view'])->name('amortisedinitialdisc.view');
        Route::get('/securities/amortisedinitialdiscController/export-pdf/{no_acc}/{id_pt}', [amortisedinitialdiscController::class, 'exportPdf'])->name('amortisedinitialdisc.exportPdf');
        Route::get('/securities/amortisedinitialdiscController/export-excel/{no_acc}/{id_pt}', [amortisedinitialdiscController::class, 'exportExcel'])->name('amortisedinitialdisc.exportExcel');

        Route::get('/securities/amortisedinitialpremController', [amortisedinitialpremController::class, 'index'])->name('securities.index');
        Route::get('/securities/amortisedinitialpremController/view/{no_acc}/{id_pt}', [amortisedinitialpremController::class, 'view'])->name('amortisedinitialprem.view');
        Route::get('/securities/amortisedinitialpremController/export-pdf/{no_acc}/{id_pt}', [amortisedinitialpremController::class, 'exportPdf'])->name('amortisedinitialprem.exportPdf');
        Route::get('/securities/amortisedinitialpremController/export-excel/{no_acc}/{id_pt}', [amortisedinitialpremController::class, 'exportExcel'])->name('amortisedinitialprem.exportExcel');

        Route::get('/securities/amortisedinitialbrokeragefeeController', [amortisedinitialbrokeragefeeController::class, 'index'])->name('amortisedinitialbrokeragefee.index');
        Route::get('/securities/amortisedinitialbrokeragefeeController/view/{no_acc}/{id_pt}', [amortisedinitialbrokeragefeeController::class, 'view'])->name('amortisedinitialbrokeragefee.view');
        Route::get('/securities/amortisedinitialbrokeragefeeController/export-pdf/{no_acc}/{id_pt}', [amortisedinitialbrokeragefeeController::class, 'exportPdf'])->name('amortisedinitialbrokeragefee.exportPdf');
        Route::get('/securities/amortisedinitialbrokeragefeeController/export-excel/{no_acc}/{id_pt}', [amortisedinitialbrokeragefeeController::class, 'exportExcel'])->name('amortisedinitialbrokeragefee.exportExcel');


        Route::get('/report-journal-securities', [journalsecuritiesController::class, 'index'])->name('report-journal-securities.index');
        Route::get('/report-journal-securities/view/{no_acc}/{id_pt}', [journalsecuritiesController::class, 'view'])->name('report-journal-securities.view');
        Route::get('/report-journal-securities/export-pdf/{id_pt}', [journalsecuritiesController::class, 'exportPdf'])->name('report-journal-securities.exportPdf');
        Route::get('/report-journal-securities/export-excel/{id_pt}', [journalsecuritiesController::class, 'exportExcel'])->name('report-journal-securities.exportExcel');
        Route::get('/report-journal-securities/export-report-excel', [journalsecuritiesController::class, 'exportReportExcel'])->name('report-journal-securities.exportReportExcel');
        Route::get('/report-journal-securities/export-csv/{id_pt}', [journalsecuritiesController::class, 'exportCsv'])->name('report-journal-securities.exportCsv');
        Route::post('/report-journal-securities/execute-procedure', [journalsecuritiesController::class, 'executeStoredProcedure'])
        ->name('securities.report-journal-securities.execute-procedure');

        Route::get('/report-journal-securities-daily', [journalsecuritiesControllerDaily::class, 'index'])->name('report-journal-securities-daily.index');
        Route::get('/report-journal-securities-daily/view/{no_acc}/{id_pt}', [journalsecuritiesControllerDaily::class, 'view'])->name('report-journal-securities-daily.view');
        Route::get('/report-journal-securities-daily/export-pdf/{id_pt}', [journalsecuritiesControllerDaily::class, 'exportPdf'])->name('report-journal-securities-daily.exportPdf');
        Route::get('/report-journal-securities-daily/export-excel/{id_pt}', [journalsecuritiesControllerDaily::class, 'exportExcel'])->name('report-journal-securities-daily.exportExcel');
        Route::get('/report-journal-securities-daily/export-report-excel', [journalsecuritiesControllerDaily::class, 'exportReportExcel'])->name('report-journal-securities-daily.exportReportExcel');
        Route::get('/report-journal-securities-daily/export-csv/{id_pt}', [journalsecuritiesControllerDaily::class, 'exportCsv'])->name('report-journal-securities-daily.exportCsv');
        Route::post('/report-journal-securities-daily/execute-procedure', [journalsecuritiesControllerDaily::class, 'executeStoredProcedure'])
        ->name('securities.report-journal-securities-daily.execute-procedure');
    });
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
    Route::get('/upload/tblcorporate', [tblcorporateController::class, 'index'])->name('corporate.index');
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

        Route::get('/outstanding', [Outstanding_SI::class, 'index'])
            ->name('simple-interest.outstanding.index');
        Route::post('/outstanding/import', [Outstanding_SI::class, 'importExcel'])
            ->name('simple-interest.outstanding.import');
        Route::post('/outstanding/execute-procedure', [Outstanding_SI::class, 'executeStoredProcedure'])
            ->name('simple-interest.outstanding.execute-procedure');
        Route::post('/outstanding/clear', [Outstanding_SI::class, 'clear'])
            ->name('simple-interest.outstanding.clear');
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

        Route::get('/outstanding', [OutstandingController::class, 'index'])
            ->name('effective.outstanding.index');
        Route::post('/outstanding/import', [OutstandingController::class, 'importExcel'])
            ->name('effective.outstanding.import');
        Route::post('/outstanding/execute-procedure', [OutstandingController::class, 'executeStoredProcedure'])
            ->name('effective.outstanding.execute-procedure');
        Route::post('/outstanding/clear', [OutstandingController::class, 'clear'])
            ->name('effective.outstanding.clear');
    });
});

Route::prefix('report-initial-recognition')->group(function () {
    Route::get('/effective', [initialRecognitionEffectiveController::class, 'index'])->name('report-initial-recognition.index');
    Route::get('/export-excel/effective/{id_pt}', [initialRecognitionEffectiveController::class, 'exportExcel'])->name('report.initial.recognition.effective.export.excel');
    Route::get('/export-pdf/effective/{id_pt}', [
       initialRecognitionEffectiveController::class,
        'exportPdf'
    ])->name('report.initial.recognition.effective.export.pdf');

    // Route::get('/effective', [InitialRecognitionController::class, 'effective'])->name('report-initial-recognition.effective');
    Route::get('/simple-interest', [initialRecognitionSimpleInterestController::class, 'index'])->name('report-initial-recognition.simple-interest');
    Route::get('/export-excel/simple-interest/{id_pt}', [initialRecognitionSimpleInterestController::class, 'exportExcel'])->name('report.initial.recognition.simple.export.excel');
    Route::get('/export-pdf/simple-interest/{id_pt}', [initialRecognitionSimpleInterestController::class, 'exportPdf'])->name('report.initial.recognition.simple.export.pdf');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/check-entity/{entity_number}', [ReportController::class, 'checkEntity'])->name('check.entity');
    Route::get('/check-account/{account_number}', [ReportController::class, 'checkAccount'])->name('check.account');
    Route::get('/check-account-corporate/{account_number}', [ReportController::class, 'checkAccountCorporate'])->name('check.account.corporate');
});
//route COA menu simple interest
Route::middleware(['auth'])->group(function () {
    Route::get('/CoA-menu-simple-interest', [COAControllerCorporateloan::class, 'index'])->name('coaSimple.index');
    Route::get('/CoA-menu-simple-interest/download-excel/{id_pt}', [COAControllerCorporateloan::class, 'exportExcel'])->name('coaSimple.downloadExcel');
});
//route COA menu effective
Route::middleware(['auth'])->group(function () {
    Route::get('/CoA-menu-effective', [COAControllerEffective::class, 'index'])->name('coaEffective.index');
    Route::get('/CoA-menu-effective/download-excel/{id_pt}', [COAControllerEffective::class, 'exportExcel'])->name('coaEffective.downloadExcel');
});
//dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [dashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard-upload', [dashboardController::class, 'uploadImage'])->name('dashboard.upload');
    Route::post('/dashboard-upload-image', [dashboardController::class, 'store'])->name('dashboard.image');
});
