<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SyLoginController;
use App\Http\Controllers\User\DashboardUser;
use App\Http\Controllers\User\Document\DocumentController;
use App\Http\Controllers\User\Document\SyDocumentController;

// Test auth
Route::get('/test-auth', function() {
    return Auth::user();
});

// Login routes
Route::get('/', [SyLoginController::class, 'showLoginForm'])->name('page.login');
Route::post('/', [SyLoginController::class, 'login'])->name('login');
Route::post('/logout', [SyLoginController::class, 'logout'])->name('page.logout');

// Group semua route protected
Route::middleware('auth')->group(function () {

    // Dashboard user
    Route::get('/user/dashboard', [DashboardUser::class, "index"])->name("user.page.dashboard");

    // Document user
    Route::get('/user/document/{parameter}', [DocumentController::class, "index"])->name("user.page.document");

    // Datatable & detail (AJAX)
   Route::post('/user/document/post/berkas', [App\Http\Controllers\User\Document\DocumentController::class, "datatable"])
    ->name("user.page.document.post.berkas");

    Route::post('/user/document/post/detail', [DocumentController::class, "detail"])->name("user.page.document.post.detail");

    // Upload PDF
    Route::post('/user/document/upload', [DocumentController::class, 'upload'])->name('user.document.upload');

    // Delete PDF
    Route::delete('/user/document/delete/{id}', [DocumentController::class, 'destroy'])->name('user.document.delete');

    // Preview PDF
    Route::get('/user/document/show/{id}', [DocumentController::class, 'show'])->name('user.document.show');

    // Detail PDF info
    Route::get('/user/document/detail/{id}', [DocumentController::class, 'detail']);
});

