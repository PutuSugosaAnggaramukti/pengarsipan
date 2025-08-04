<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SyLoginController;
use App\Http\Controllers\User\DashboardUser;
use App\Http\Controllers\User\Document\DocumentController;
use App\Http\Controllers\User\Document\SyDocumentController;
use App\Http\Controllers\User\RecycleBinController;
use App\Http\Controllers\User\Agunan\AgunanController;
use App\Http\Controllers\User\Berkas\BerkasController;
use App\Http\Controllers\User\Berkas\SyBerkasController;

// Test auth
Route::get('/test-auth', function() {
    return Auth::user();
});

// ROUTE LOGIN
Route::get('/', function() {
    return view('user.login');
})->name('page.login');

Route::post('/', [SyLoginController::class, 'loginSy'])->name('page.login.post');
Route::get('/logout', [SyLoginController::class, 'logoutSy'])->name('page.logout.sy');

// Group semua route yang perlu middleware
Route::middleware(['web', 'auth'])->group(function () {

    // DASHBOARD
    Route::get('/User/Dashboard',[DashboardUser::class,"index"])->name("user.page.dashboard");

    // DOCUMENT
Route::get('/User/Document',[DocumentController::class,"index"])->name("user.page.document");
Route::post("/User/Document/",[SyDocumentController::class,"tambahData"])->name("user.page.document.tambah");
Route::get('/User/Document/{parameter}', [DocumentController::class, 'dataDocument'])->name('user.page.document.param');
Route::post('/User/document/mass-delete', [SyDocumentController::class, 'hapusBanyak'])->name('user.page.document.massDelete');
Route::post('/User/Document/post/Document', [SyDocumentController::class,"document"])->name("user.page.document.post.document");
Route::post('/User/Document/POST/DetailDocument', [SyDocumentController::class, 'detail'])->name('user.page.document.post.detail');


    //AGUNAN    
    Route::get('/User/Agunan', [AgunanController::class, "index"])->name("user.page.agunan");
    Route::post('/User/Agunan', [AgunanController::class, "tambahData"])->name("user.page.agunan.tambah");
    Route::get('/User/Agunan/{parameter}', [AgunanController::class, "dataAgunan"])->name("user.page.agunan.param");
    Route::post('/User/Agunan/POST/Agunan', [AgunanController::class, "agunan"])->name("user.page.agunan.post.agunan");
    Route::post('/User/Agunan/POST/DetailAgunan', [AgunanController::class, "detail"])->name("user.page.agunan.post.detail");
    Route::post('/User/Agunan/mass-delete', [AgunanController::class, 'hapusBanyak'])->name('user.page.agunan.massDelete');

    // FILE PREVIEW + RECYCLE BIN
    Route::get('/document/preview/{tahun}/{file}', [SyDocumentController::class, 'preview'])->name('document.preview');
    Route::get('/agunan/preview/{tahun}/{file}', [AgunanController::class, 'preview'])->name('agunan.preview');
    Route::get('document/delete/{id}', [SyDocumentController::class, 'softDelete'])->name('user.page.document.softDelete');
    Route::delete('/agunan/delete/{id}', [AgunanController::class, 'softDelete'])->name('agunan.softdelete');

    Route::prefix('recycle-bin')->group(function() {
        Route::get('/', [RecycleBinController::class, 'index'])->name('user.recyclebin');
        Route::get('/restore/document/{id}', [RecycleBinController::class, 'restoreDocument'])->name('user.recyclebin.restore.document');
        Route::delete('/force-delete/document/{id}', [RecycleBinController::class, 'forceDeleteDocument'])->name('user.recyclebin.forceDelete.document');

        Route::get('/restore/agunan/{id}', [RecycleBinController::class, 'restoreAgunan'])->name('user.recyclebin.restore.agunan');
        Route::delete('/force-delete/agunan/{id}', [RecycleBinController::class, 'forceDeleteAgunan'])->name('user.recyclebin.forceDelete.agunan');
        Route::post('/recyclebin/restore/bulk', [RecycleBinController::class, 'bulkRestore'])->name('user.recyclebin.bulkRestore');
        Route::delete('/recyclebin/delete/bulk', [RecycleBinController::class, 'bulkDelete'])->name('user.recyclebin.bulkDelete');
    });

    



});
